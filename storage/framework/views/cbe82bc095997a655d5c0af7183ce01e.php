<?php $__env->startSection('title', __('Edit Category')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<style>
.icon-picker {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
}

.icon-option {
    width: 60px;
    height: 60px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.icon-option:hover, .icon-option.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
    transform: scale(1.1);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.image-upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.image-upload-area:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
}

.image-preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.current-image-preview {
    position: relative;
    display: inline-block;
}

.current-image-preview img {
    max-width: 200px;
    max-height: 150px;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> <?php echo e(__('Edit Category')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('Update category details')); ?></p>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong><?php echo e(__('Oops! Something went wrong:')); ?></strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> <?php echo e(__('Category Information')); ?></h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('categories.update', $category->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="form-row">
                <input type="hidden" id="name_fr" name="name_fr" value="<?php echo e(old('name_fr', $category->name_fr ?: $category->name)); ?>">
                <input type="hidden" id="name_en" name="name_en" value="<?php echo e(old('name_en', $category->name_en)); ?>">
                <input type="hidden" name="name" id="name" value="<?php echo e(old('name', $category->name)); ?>">
                
                <div class="form-group">
                    <label for="name_ar" class="form-label"><?php echo e(__('Category Name (AR)')); ?> <span class="required">*</span></label>
                    <input type="text" id="name_ar" name="name_ar" class="form-control" value="<?php echo e(old('name_ar', $category->name_ar)); ?>" placeholder="<?php echo e(__('Category Name (AR)')); ?>" dir="rtl" required oninput="syncTranslations()">
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label"><?php echo e(__('Slug')); ?></label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           class="form-control" 
                           value="<?php echo e(old('slug', $category->slug)); ?>" 
                           placeholder="<?php echo e(__('auto-generated from name')); ?>">
                    <small class="form-help"><?php echo e(__('Leave empty to auto-generate')); ?></small>
                </div>
            </div>

            <input type="hidden" id="description_fr" name="description_fr" value="<?php echo e(old('description_fr', $category->description_fr ?: $category->description)); ?>">
            <input type="hidden" id="description_en" name="description_en" value="<?php echo e(old('description_en', $category->description_en)); ?>">
            <input type="hidden" name="description" id="description" value="<?php echo e(old('description', $category->description)); ?>">

            <div class="form-group mt-3">
                <label for="description_ar" class="form-label"><?php echo e(__('Description (AR)')); ?></label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="2" placeholder="<?php echo e(__('Description (AR)')); ?>..." dir="rtl" oninput="syncTranslations()"><?php echo e(old('description_ar', $category->description_ar)); ?></textarea>
            </div>

            <div class="form-row mt-4">
                <div class="form-group">
                    <label for="parent_id" class="form-label"><?php echo e(__('Parent Category')); ?></label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">-- <?php echo e(__('No Parent (Top Level)')); ?> --</option>
                        <?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($parent->id !== $category->id): ?> 
                            <option value="<?php echo e($parent->id); ?>" <?php echo e(old('parent_id', $category->parent_id) == $parent->id ? 'selected' : ''); ?>>
                                <?php echo e($parent->name); ?>

                            </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="form-help"><?php echo e(__('Create a subcategory by selecting a parent')); ?></small>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">
                        <?php echo e(__('Status')); ?> <span class="required">*</span>
                    </label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" <?php echo e(old('status', $category->status) == 'active' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                        <option value="inactive" <?php echo e(old('status', $category->status) == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sort_order" class="form-label"><?php echo e(__('Sort Order')); ?></label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           class="form-control" 
                           value="<?php echo e(old('sort_order', $category->sort_order)); ?>" 
                           min="0">
                    <small class="form-help"><?php echo e(__('Lower numbers appear first')); ?></small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?php echo e(__('Icon (FontAwesome)')); ?></label>
                <input type="text" 
                       id="icon" 
                       name="icon" 
                       class="form-control" 
                       value="<?php echo e(old('icon', $category->icon)); ?>" 
                       placeholder="e.g., fas fa-laptop">
                <small class="form-help"><?php echo e(__('Enter FontAwesome class or select from popular icons:')); ?></small>
                <div class="icon-picker" style="margin-top: 12px;">
                    <?php $__currentLoopData = ['fas fa-laptop', 'fas fa-mobile', 'fas fa-tshirt', 'fas fa-book', 'fas fa-utensils', 'fas fa-home', 'fas fa-car', 'fas fa-gamepad', 'fas fa-music', 'fas fa-camera', 'fas fa-toolbox', 'fas fa-heart']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="icon-option <?php echo e(old('icon', $category->icon) == $iconClass ? 'selected' : ''); ?>" onclick="selectIcon('<?php echo e($iconClass); ?>')">
                            <i class="<?php echo e($iconClass); ?>" style="font-size: 24px; color: #667eea;"></i>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?php echo e(__('Category Image')); ?></label>
                
                <?php if($category->image): ?>
                <div class="mb-3">
                    <p class="text-muted mb-2"><small><?php echo e(__('Current Image:')); ?></small></p>
                    <div class="current-image-preview">
                        <img src="<?php echo e(Storage::url($category->image)); ?>" alt="<?php echo e($category->translated_name); ?>">
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image" value="1">
                        <label class="form-check-label text-danger" for="remove_image">
                            <i class="fas fa-trash me-1"></i> <?php echo e(__('Remove current image')); ?>

                        </label>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('image').click()">
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="d-none" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <div id="imagePreview" class="image-preview-container" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 180px; border-radius: 8px;">
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage(event)">
                            <i class="fas fa-times me-1"></i> <?php echo e(__('Remove')); ?>

                        </button>
                    </div>
                    <div id="uploadPlaceholder" class="text-center py-4">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: #667eea; opacity: 0.6;"></i>
                        <p class="mb-0 mt-2 text-muted"><?php echo e($category->image ? __('Upload a new image to replace') : __('Click to upload or drag and drop')); ?></p>
                        <small class="text-muted"><?php echo e(__('PNG, JPG up to 2MB')); ?></small>
                    </div>
                </div>
                <small class="form-help"><?php echo e($category->image ? __('Upload a new image to replace the current one, or check the box above to remove it') : __('Optional image to represent this category')); ?></small>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo e(__('Update Category')); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'flex';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(event) {
    event.stopPropagation();
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    input.value = '';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
}
function syncTranslations() {
    const nameAr = document.getElementById('name_ar').value;
    const descAr = document.getElementById('description_ar').value;
    
    // Sync names
    document.getElementById('name_fr').value = nameAr;
    document.getElementById('name_en').value = nameAr;
    document.getElementById('name').value = nameAr;
    
    // Sync descriptions (checking if hidden fields exist)
    if(document.getElementById('description_fr')) document.getElementById('description_fr').value = descAr;
    if(document.getElementById('description_en')) document.getElementById('description_en').value = descAr;
    if(document.getElementById('description')) document.getElementById('description').value = descAr;
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/categories/edit.blade.php ENDPATH**/ ?>