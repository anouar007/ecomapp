<?php $__env->startSection('meta_title', 'تأكيد الطلب — ' . setting('app_name', 'Hijab Princesses')); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Checkout Mobile-First Layout ────────────────── */
.checkout-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 1rem 0 3rem;
}

.checkout-wrapper {
    max-width: 540px;
    margin: 0 auto;
    padding: 0 0.75rem;
}

/* ── Page Header ─────────────────────────────────── */
.checkout-header {
    text-align: center;
    margin-bottom: 1.5rem;
    padding: 0 1rem;
}
.checkout-header h1 {
    font-size: 1.5rem;
    font-weight: 900;
    color: #1e293b;
    margin-bottom: 0.25rem;
}
.checkout-header p {
    font-size: 0.85rem;
    color: #64748b;
}

/* ── Order Summary Card ───────────────────────────── */
.summary-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 1.25rem;
    border: 1px solid #f1f5f9;
}
.summary-header {
    background: #1e293b;
    color: #fff;
    padding: 0.875rem 1.25rem;
    font-weight: 800;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.summary-body {
    padding: 1rem;
}
.cart-row {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.cart-row:last-child { border-bottom: none; }
.cart-img-wrap {
    position: relative;
    flex-shrink: 0;
}
.cart-img {
    width: 60px;
    height: 85px;
    object-fit: cover;
    border-radius: 0.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.cart-img-placeholder {
    width: 60px;
    height: 85px;
    background: #f1f5f9;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    font-size: 1.25rem;
}
.cart-qty-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 22px;
    height: 22px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}
.cart-info { flex: 1; min-width: 0; }
.cart-name {
    font-weight: 800;
    font-size: 0.95rem;
    color: #1e293b;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}
.cart-variants {
    display: flex;
    gap: 0.375rem;
    flex-wrap: wrap;
    align-items: center;
}
.variant-tag {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 100px;
}
.cart-price {
    font-weight: 900;
    color: var(--accent);
    font-size: 1rem;
    flex-shrink: 0;
}

/* ── Totals ──────────────────────────────────────── */
.totals-section {
    border-top: 2px dashed #e2e8f0;
    padding-top: 1rem;
    margin-top: 1rem;
}
.totals-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
}
.totals-row.grand {
    font-size: 1.2rem;
    font-weight: 900;
    color: #1e293b;
    border-top: 1px solid #e2e8f0;
    padding-top: 0.75rem;
    margin-top: 0.5rem;
}
.totals-row.grand .val { color: var(--accent); }

/* ── Form Card ───────────────────────────────────── */
.form-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 1.25rem;
    border: 1px solid #f1f5f9;
}
.form-header {
    background: #f8fafc;
    padding: 1rem 1.25rem;
    font-weight: 900;
    font-size: 1.05rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.form-header i { color: var(--accent); }
.form-body { padding: 1.25rem 1rem; }

.field-group { margin-bottom: 1rem; }
.field-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 800;
    color: #334155;
    margin-bottom: 0.4rem;
}
.field-input {
    width: 100%;
    background: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    transition: all 0.2s;
    outline: none;
    font-family: inherit;
}
.field-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-light);
}
.field-input::placeholder { color: #94a3b8; font-weight: 500; }

/* TomSelect overrides */
.ts-wrapper { width: 100%; }
.ts-control {
    background: #fff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 0.75rem !important;
    padding: 0.75rem 1rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    min-height: unset !important;
    box-shadow: none !important;
    cursor: pointer;
}
.ts-control:focus-within,
.ts-wrapper.focus .ts-control {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px var(--accent-light) !important;
}
.ts-control input { font-size: 1rem !important; font-weight: 600 !important; }
.ts-dropdown {
    border-radius: 0.75rem !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    border: 1px solid #e2e8f0 !important;
    overflow: hidden;
}
.ts-dropdown .option { padding: 0.75rem 1rem; font-size: 0.95rem; font-weight: 600; }
.ts-dropdown .option.active { background: var(--accent-light) !important; color: var(--accent) !important; }
.ts-dropdown .option:hover { background: #f8fafc !important; }

/* ── Payment Method ──────────────────────────────── */
.payment-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.25rem;
    text-align: center;
    margin-bottom: 1.25rem;
}
.payment-icon {
    width: 50px; height: 50px;
    background: var(--accent-light);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 1.25rem;
    margin: 0 auto 0.75rem;
}
.payment-label {
    font-weight: 900;
    font-size: 1.1rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}
.payment-note {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

/* ── Checkout Button ─────────────────────────────── */
.btn-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    background: linear-gradient(135deg, var(--accent), #a07840);
    color: #fff;
    border: none;
    border-radius: 0.875rem;
    padding: 0.9rem 1.25rem;
    font-weight: 700;
    font-size: 1.15rem;
    box-shadow: 0 4px 16px rgba(197,160,89,0.4);
    transition: transform 0.15s, box-shadow 0.15s;
    margin-top: 1rem;
    margin-bottom: 0.6rem;
}
.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(197,160,89,0.45);
    color: #fff;
}
.btn-checkout:active { transform: translateY(0); }

.back-link {
    display: block;
    text-align: center;
    font-size: 0.875rem;
    color: #94a3b8;
    text-decoration: none;
}
.back-link:hover { color: #64748b; }

/* ── Trust Pills ─────────────────────────────────── */
.trust-row {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}
.trust-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 600;
}
.trust-item i { font-size: 1.25rem; color: #c5a059; }

.checkout-grid {
    display: flex;
    flex-direction: column-reverse; /* Cart before Form on Mobile */
    gap: 1.5rem;
}

/* ── Desktop: show order summary beside form ─────── */
@media (min-width: 992px) {
    .checkout-page { padding: 3rem 0 4rem; }
    .checkout-wrapper { max-width: 1100px; }
    .checkout-header h1 { font-size: 2.2rem; }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2.5rem;
        align-items: start;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="checkout-page">
    <div class="checkout-wrapper">

        
        <div class="checkout-header">
            <h1>تأكيد الطلب</h1>
            <p class="mb-2">شحن سريع لجميع مدن المغرب 🇲🇦</p>
            <p class="small text-gold fw-bold mb-0"><i class="fas fa-info-circle me-1"></i> التوصيل: من 20 د.م. إلى 40 د.م. حسب المدينة</p>
        </div>

        <div class="checkout-grid">
            <div class="checkout-main">
                
                <div class="form-card">
                    <div class="form-header">
                        <i class="fas fa-map-marker-alt text-primary"></i> معلومات التوصيل
                    </div>
                    <div class="form-body">
                        <form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkout-form">
                            <?php echo csrf_field(); ?>

                            <div class="field-group">
                                <label class="field-label" for="customer_name">الاسم الكامل</label>
                                <input type="text" id="customer_name" name="customer_name"
                                       class="field-input" placeholder="مثال: فاطمة الزهراء" required
                                       value="<?php echo e(old('customer_name')); ?>">
                            </div>

                            <div class="row g-3 field-group mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="field-label" for="customer_phone">رقم الهاتف</label>
                                    <input type="tel" id="customer_phone" name="customer_phone"
                                           class="field-input" placeholder="06 XX XX XX XX" required
                                           value="<?php echo e(old('customer_phone')); ?>">
                                    <?php $__errorArgs = ['customer_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1 fw-bold"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="field-label" for="customer_phone_confirmation">تأكيد رقم الهاتف</label>
                                    <input type="tel" id="customer_phone_confirmation" name="customer_phone_confirmation"
                                           class="field-input" placeholder="06 XX XX XX XX" required
                                           value="<?php echo e(old('customer_phone_confirmation')); ?>">
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="shipping_address">العنوان</label>
                                <input type="text" id="shipping_address" name="shipping_address"
                                       class="field-input" placeholder="الحي، الشارع، رقم المنزل..." required
                                       value="<?php echo e(old('shipping_address')); ?>">
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="city-select">المدينة</label>
                                <select name="shipping_city" id="city-select" required>
                                    <option value="">ابحثي عن مدينتك...</option>
                                </select>
                            </div>
                        </form>

                        <button type="submit" form="checkout-form" class="btn-checkout">
                            <i class="fas fa-check-circle"></i> تأكيد الطلب الآن
                        </button>

                        <a href="<?php echo e(route('cart.index')); ?>" class="back-link">
                            <i class="fas fa-arrow-right me-1"></i> العودة للسلة
                        </a>
                    </div>
                </div>

                
                <div class="payment-card d-lg-none mt-3">
                    <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="payment-label">الدفع عند الاستلام</div>
                    <div class="payment-note">سيتواصل معك فريقنا لتأكيد الطلب قبل الإرسال</div>
                </div>
            </div>

            <div class="checkout-side">
                
                <div class="summary-card">
                    <div class="summary-header">
                        <i class="fas fa-shopping-bag"></i> ملخص الطلب
                    </div>
                    <div class="summary-body">
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cart-row">
                            <div class="cart-img-wrap">
                                <?php if($details['image']): ?>
                                    <img src="<?php echo e(Storage::url($details['image'])); ?>" alt="<?php echo e($details['name']); ?>" class="cart-img">
                                <?php else: ?>
                                    <div class="cart-img-placeholder"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <div class="cart-qty-badge"><?php echo e($details['quantity']); ?></div>
                            </div>
                            <div class="cart-info">
                                <div class="cart-name"><?php echo e($details['name']); ?></div>
                                <div class="cart-variants d-flex align-items-center gap-2 mt-1">
                                    <?php if($details['image']): ?>
                                        <div class="rounded-circle border border-gold-light overflow-hidden shadow-sm" style="width: 35px; height: 35px;">
                                            <img src="<?php echo e(Storage::url($details['image'])); ?>" alt="Style" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    <?php endif; ?>
                                    <?php if($details['size'] ?? null): ?>
                                        <span class="variant-tag"><?php echo e($details['size']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="mc-qty" style="border-radius: 6px; border: 1px solid #e2e8f0; display: inline-flex;">
                                        <button type="button" class="mc-qty-btn" onclick="updateCheckoutQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] - 1); ?>)"><i class="fas fa-minus"></i></button>
                                        <input type="text" class="mc-qty-val" value="<?php echo e($details['quantity']); ?>" readonly style="width: 30px; text-align: center; border: none; background: transparent; font-weight: bold; color: #1e293b;">
                                        <button type="button" class="mc-qty-btn" onclick="updateCheckoutQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] + 1); ?>)"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <button type="button" class="btn btn-sm text-danger p-0" onclick="removeCheckoutItem('<?php echo e($key); ?>')" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="cart-price d-flex flex-column align-items-end">
                                <span class="fw-bold text-gold"><?php echo e(currency($details['price'] * $details['quantity'])); ?></span>
                                <?php if(isset($details['original_price']) && $details['original_price'] > $details['price']): ?>
                                    <span class="text-danger small text-decoration-line-through" style="font-size: 0.75rem;"><?php echo e(currency($details['original_price'] * $details['quantity'])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div class="totals-section">
                            <div class="totals-row">
                                <span>المجموع الفرعي</span>
                                <span class="fw-bold"><?php echo e(currency($total)); ?></span>
                            </div>
                             <div class="totals-row">
                                <span>التوصيل</span>
                                <span class="fw-bold" id="shipping-cost-display">حدد المدينة...</span>
                            </div>
                            <div class="totals-row grand">
                                <span>الإجمالي</span>
                                <span class="val fs-6 text-muted fw-normal" id="grand-total-display">يُحدد بعد اختيار المدينة</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="payment-card d-none d-lg-block">
                    <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="payment-label">الدفع عند الاستلام</div>
                    <div class="payment-note">سيتواصل معك فريقنا لتأكيد الطلب قبل الإرسال</div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inject cities from PHP
    const moroccanCities = <?php echo json_encode($cities, 15, 512) ?>;

    const options = moroccanCities.map(c => ({
        value: c.arabic_name,
        label: c.arabic_name,
        fr: c.name,
        price: parseFloat(c.price),
        text: c.arabic_name + ' ' + c.name,
    }));

    const subtotal = <?php echo e($total); ?>;
    const shippingDisplay = document.getElementById('shipping-cost-display');
    const totalDisplay = document.getElementById('grand-total-display');

    const ts = new TomSelect('#city-select', {
        options: options,
        items: [],
        valueField: 'value',
        labelField: 'label',
        searchField: ['text'],
        render: {
            option: function (data, escape) {
                return '<div class="d-flex align-items-center justify-content-between gap-2">' +
                    '<div>' +
                        '<span class="fw-bold">' + escape(data.label) + '</span>' +
                        '<span class="text-muted small ms-2 opacity-75">' + escape(data.fr || '') + '</span>' +
                    '</div>' +
                    '<span class="badge bg-light text-dark fw-normal" style="font-size: 0.75rem;">' + escape(data.price) + ' د.م.</span>' +
                '</div>';
            },
            item: function (data, escape) {
                return '<div>' + escape(data.label) + '</div>';
            },
        },
        placeholder: 'ابحثي عن مدينتك... / Chercher...',
        create: function(input) {
            return {
                value: input,
                label: input,
                fr: 'إدخال يدوي',
                text: input,
                price: 40
            };
        },
        createOnBlur: true,
        maxOptions: 500,
        onChange: function(value) {
            // Find in original options, or fallback to the custom created object from TomSelect
            const city = options.find(o => o.value === value) || this.options[value];
            updateShipping(city);
        }
    });

    function updateShipping(city) {
        if (!city) {
            shippingDisplay.textContent = 'حدد المدينة...';
            totalDisplay.textContent = 'يُحدد بعد اختيار المدينة';
            totalDisplay.classList.add('fs-6', 'text-muted', 'fw-normal');
            return;
        }

        const cost = city.price;
        const total = subtotal + cost;

        shippingDisplay.textContent = cost.toFixed(2) + ' د.م.';
        totalDisplay.textContent = total.toFixed(2) + ' د.م.';
        totalDisplay.classList.remove('fs-6', 'text-muted', 'fw-normal');
        
        shippingDisplay.classList.add('text-primary');
        setTimeout(() => shippingDisplay.classList.remove('text-primary'), 500);
    }

    // restore form
    if(sessionStorage.getItem('chk_name')) {
        document.getElementById('customer_name').value = sessionStorage.getItem('chk_name');
        document.getElementById('customer_phone').value = sessionStorage.getItem('chk_phone1');
        document.getElementById('customer_phone_confirmation').value = sessionStorage.getItem('chk_phone2');
        document.getElementById('shipping_address').value = sessionStorage.getItem('chk_address');
        setTimeout(() => {
            const savedCity = sessionStorage.getItem('chk_city');
            if(savedCity) {
                ts.setValue(savedCity);
            }
            // clear it so it doesn't persist forever
            sessionStorage.removeItem('chk_name');
            sessionStorage.removeItem('chk_phone1');
            sessionStorage.removeItem('chk_phone2');
            sessionStorage.removeItem('chk_address');
            sessionStorage.removeItem('chk_city');
        }, 100);
    }

    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const phone1 = document.getElementById('customer_phone').value;
            const phone2 = document.getElementById('customer_phone_confirmation').value;
            
            // Clean phone (keep only digits)
            const cleanPhone = phone1.replace(/\D/g, '');
            let isPhoneValid = false;
            
            if (cleanPhone.length === 10 && (cleanPhone.startsWith('05') || cleanPhone.startsWith('06') || cleanPhone.startsWith('07'))) {
                isPhoneValid = true;
            } else if (cleanPhone.length === 12 && cleanPhone.startsWith('212') && ['5', '6', '7'].includes(cleanPhone.substring(3, 4))) {
                isPhoneValid = true;
            }

            if (!isPhoneValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى إدخال رقم هاتف مغربي صحيح (مثال: 0612345678)',
                    confirmButtonColor: '#c5a059',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            if (phone1 !== phone2) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'أرقام الهاتف غير متطابقة، يرجى التأكد.',
                    confirmButtonColor: '#c5a059',
                    confirmButtonText: 'حسناً'
                });
                return;
            }
        });
    }
});

function saveCheckoutForm() {
    sessionStorage.setItem('chk_name', document.getElementById('customer_name').value || '');
    sessionStorage.setItem('chk_phone1', document.getElementById('customer_phone').value || '');
    sessionStorage.setItem('chk_phone2', document.getElementById('customer_phone_confirmation').value || '');
    sessionStorage.setItem('chk_address', document.getElementById('shipping_address').value || '');
    sessionStorage.setItem('chk_city', document.getElementById('city-select').value || '');
}

window.updateCheckoutQty = function(id, qty) {
    if(qty < 1) {
        removeCheckoutItem(id);
        return;
    }
    
    saveCheckoutForm();
    
    fetch('<?php echo e(route('cart.update')); ?>', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id, quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            Swal.fire({ icon: 'warning', title: data.message || 'الكمية غير متوفرة' });
        }
    });
};

window.removeCheckoutItem = function(id) {
    Swal.fire({
        title: 'حذف المنتج؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'إلغاء',
        confirmButtonText: 'نعم، احذف!'
    }).then((result) => {
        if (result.isConfirmed) {
            saveCheckoutForm();
            fetch('<?php echo e(route('cart.remove')); ?>', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if(data.cartCount === 0) {
                    window.location.href = '<?php echo e(route('cart.index')); ?>';
                } else {
                    window.location.reload();
                }
            });
        }
    });
};
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/checkout/index.blade.php ENDPATH**/ ?>