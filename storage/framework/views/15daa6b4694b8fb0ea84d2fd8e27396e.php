<?php $__env->startSection('meta_title', 'تواصل معنا — ديوان أكاديمية الخط العربي'); ?>
<?php $__env->startSection('meta_description', 'تواصل مع مركز ديوان للخط العربي — زيارة، استفسار، أو تسجيل في دورة.'); ?>

<?php $__env->startSection('content'); ?>

<section style="background-color: #fcfbf9; padding: 100px 0 100px;" dir="ltr">
    <div class="container">

        <div class="row g-5 align-items-start">

            
            <div class="col-lg-5" dir="rtl">
                <div class="pe-lg-4">
                    <span class="d-block mb-3" style="color: #c9a65d; font-size: 0.95rem; font-weight: 700; letter-spacing: 0; font-family: var(--font-heading);">تواصل معنا</span>
                    <h1 class="fw-bold mb-4" style="font-family: var(--font-heading); font-size: clamp(1.8rem, 2.5vw, 2.4rem); color: #3b4d1b; line-height: 1.4;">
                        استفسر وقم بزيارة مركزنا
                    </h1>
                    <p class="mb-5" style="color: #555; font-size: 1rem; line-height: 1.8;">
                        سواء كنت مبتدئًا أو فنانًا متقدمًا، فإن أبوابنا مفتوحة لرحلتك في عالم الخط العربي.
                    </p>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #f4efdf; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="color: #4A5D23; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-1" style="font-size: 0.9rem; color: #3b4d1b; font-family: var(--font-heading);">عنوان المركز</p>
                            <p class="mb-0" style="font-size: 0.85rem; color: #666;">بريستيج الألفة - الدار البيضاء</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #f4efdf; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone-alt" style="color: #4A5D23; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-1" style="font-size: 0.9rem; color: #3b4d1b; font-family: var(--font-heading);">رقم الهاتف</p>
                            <p class="mb-0" style="font-size: 0.85rem; color: #666;" dir="ltr">06 66 61 33 98</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-5">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #f4efdf; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="color: #4A5D23; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-1" style="font-size: 0.9rem; color: #3b4d1b; font-family: var(--font-heading);">البريد الإلكتروني</p>
                            <p class="mb-0" style="font-size: 0.85rem; color: #666;" dir="ltr">art.diwane@gmail.com</p>
                        </div>
                    </div>

                    
                    <div class="rounded-3 overflow-hidden position-relative mt-2" style="height: 240px; border: 1px solid #eee;">
                        <img src="<?php echo e(asset('images/map-placeholder.jpg')); ?>" onerror="this.src='https://maps.googleapis.com/maps/api/staticmap?center=Casablanca,Morocco&zoom=13&size=600x300&maptype=roadmap&markers=color:green%7CCasablanca,Morocco&style=feature:all|element:labels|visibility:off&style=feature:landscape|color:0xf2efe9&style=feature:water|color:0xccdbe0&style=feature:road|color:0xffffff&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU3Lk'" alt="Map" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.95);">
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; background: #3b4d1b;">
                                <span style="color: #c9a65d; font-weight: 900; font-size: 1.4rem; font-family: var(--font-heading);">D</span>
                            </div>
                        </div>
                        <div class="position-absolute bg-white p-3 shadow-sm" style="bottom: 15px; left: 15px; border-radius: 4px; text-align: left;" dir="ltr">
                            <p class="fw-bold mb-1" style="color: #888; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Location Details</p>
                            <p class="mb-0 fw-bold" style="color: #3b4d1b; font-size: 0.85rem;">Al-Qalam Main Studio</p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-7" dir="rtl">
                <div class="bg-white rounded-3 p-5" style="border: 1px solid #e0d8cc; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                    <form>
                        <?php echo csrf_field(); ?>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2" style="font-size: 0.85rem; color: #3b4d1b; font-family: var(--font-heading);">الاسم الكامل</label>
                                <input type="text" class="form-control" placeholder="E.g. Omar Khalid" style="border-radius: 4px; border: 1px solid #ddd; font-size: 0.9rem; padding: 0.8rem 1rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2" style="font-size: 0.85rem; color: #3b4d1b; font-family: var(--font-heading);">البريد الإلكتروني</label>
                                <input type="email" class="form-control text-start" placeholder="omar.k@example.com" style="border-radius: 4px; border: 1px solid #ddd; font-size: 0.9rem; padding: 0.8rem 1rem;" dir="ltr">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2" style="font-size: 0.85rem; color: #3b4d1b; font-family: var(--font-heading);">رقم الهاتف</label>
                                <input type="tel" class="form-control text-start" placeholder="+212 ..." style="border-radius: 4px; border: 1px solid #ddd; font-size: 0.9rem; padding: 0.8rem 1rem;" dir="ltr">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2" style="font-size: 0.85rem; color: #3b4d1b; font-family: var(--font-heading);">الدورة المختارة</label>
                                <select class="form-select" style="border-radius: 4px; border: 1px solid #ddd; font-size: 0.9rem; padding: 0.8rem 1rem; color: #777;">
                                    <option value="" selected>اختر</option>
                                    <option>خط النسخ</option>
                                    <option>خط الرقعة</option>
                                    <option>Diwani Script Mastery</option>
                                    <option>دورة فن الحروفيات</option>
                                    <option>Foundations of Naskh</option>
                                    <option>Expressive Ink & Lettering</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold mb-2" style="font-size: 0.85rem; color: #3b4d1b; font-family: var(--font-heading);">رسالة أو طلبات خاصة</label>
                            <textarea class="form-control" rows="5" placeholder="رسالة" style="border-radius: 4px; border: 1px solid #ddd; font-size: 0.9rem; padding: 0.8rem 1rem; resize: none;"></textarea>
                        </div>

                        <button type="submit" class="btn fw-bold w-100 py-3 d-flex justify-content-center align-items-center gap-2 hover-scale" style="background-color: #4A5D23; color: #fff; border-radius: 4px; font-family: var(--font-heading); font-size: 1.1rem; border: none;">
                            <i class="fas fa-book-open"></i> ابدأ رحلتك
                        </button>
                        <p class="text-center mt-4 mb-0" style="font-size: 0.8rem; color: #888;">
                            بالنقر، فإنك توافق على شروط التسجيل وسياسة الخصوصية الخاصة بنا.
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/contact.blade.php ENDPATH**/ ?>