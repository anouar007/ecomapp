<?php $__env->startSection('title', __('Settings')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .settings-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .settings-header {
        margin-bottom: 30px;
    }

    .settings-title {
        font-size: 32px;
        font-weight: 700;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 8px 0;
    }

    .settings-subtitle {
        color: #64748b;
        font-size: 14px;
    }

    .settings-layout {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 24px;
    }

    .settings-tabs {
        background: white;
        border-radius: 16px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .settings-tab {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    .settings-tab:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .settings-tab.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .settings-tab i {
        font-size: 18px;
        width: 20px;
    }

    .settings-content {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .settings-section {
        margin-bottom: 32px;
    }

    .settings-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #334155;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .color-picker-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .color-input-wrapper {
        position: relative;
    }

    .color-input {
        width: 100%;
        height: 50px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .color-input:hover {
        border-color: #3b82f6;
    }

    .color-value {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        font-weight: 600;
        font-size: 12px;
        color: #1e293b;
        pointer-events: none;
        text-transform: uppercase;
    }

    .logo-upload-area {
        border: 2px d

ashed #cbd5e1;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .logo-upload-area:hover {
        border-color: #3b82f6;
        background: #f8fafc;
    }

    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        margin: 0 auto 16px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f1f5f9;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    @media (max-width: 968px) {
        .settings-layout {
            grid-template-columns: 1fr;
        }

        .settings-tabs {
            position: static;
            display: flex;
            overflow-x: auto;
            gap: 8px;
        }

        .settings-tab {
            flex-shrink: 0;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="settings-container">
    <div class="settings-header">
        <h1 class="settings-title"><?php echo e(__('Settings')); ?></h1>
        <p class="settings-subtitle"><?php echo e(__('Customize your application preferences and configurations')); ?></p>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444;">
        <i class="fas fa-exclamation-circle"></i>
        <div style="margin-left: 8px;">
            <ul style="margin: 0; padding-left: 16px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="settings-layout">
        <!-- Tabs -->
        <div class="settings-tabs">
            <button class="settings-tab" onclick="switchTab('general')">
                <i class="fas fa-sliders-h"></i>
                <span><?php echo e(__('General')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('company')">
                <i class="fas fa-building"></i>
                <span><?php echo e(__('Company Info')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('theme')">
                <i class="fas fa-palette"></i>
                <span><?php echo e(__('Theme')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('social')">
                <i class="fas fa-share-alt"></i>
                <span><?php echo e(__('Social Media')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('frontend')">
                <i class="fas fa-desktop"></i>
                <span><?php echo e(__('Frontend')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('localization')">
                <i class="fas fa-globe"></i>
                <span><?php echo e(__('Localization')); ?></span>
            </button>
            <button class="settings-tab" onclick="switchTab('advanced')">
                <i class="fas fa-cogs"></i>
                <span><?php echo e(__('Advanced')); ?></span>
            </button>
        </div>

        <!-- Content -->
        <div class="settings-content">
            <form action="<?php echo e(route('settings.update')); ?>" method="POST" id="settings-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- General Tab -->
                <div class="tab-pane active" id="general-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Application Information')); ?></h3>
                        
                        <?php
                            $generalSettings = $settings->get('general', collect());
                        ?>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Application Name')); ?></label>
                            <input type="text" name="settings[app_name]" class="form-input" 
                                   value="<?php echo e($generalSettings->where('key', 'app_name')->first()?->value ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Application Description')); ?></label>
                            <input type="text" name="settings[app_description]" class="form-input" 
                                   value="<?php echo e($generalSettings->where('key', 'app_description')->first()?->value ?? ''); ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Logo')); ?></h3>
                        
                        <?php if(setting('app_logo')): ?>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <img src="<?php echo e(asset('storage/' . setting('app_logo'))); ?>" alt="<?php echo e(__('Logo')); ?>" class="logo-preview">
                                    <button type="submit" form="remove-logo-form" class="btn-danger" onclick="return confirm('<?php echo e(__('Remove logo?')); ?>')">
                                        <i class="fas fa-trash"></i> <?php echo e(__('Remove Logo')); ?>

                                    </button>
                            </div>
                        <?php endif; ?>

                            <div class="logo-upload-area" onclick="document.getElementById('logo-input').click()">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #3b82f6; margin-bottom: 16px;"></i>
                                <p style="margin: 0; color: #64748b;"><?php echo e(__('Click to upload logo')); ?></p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;"><?php echo e(__('PNG, JPG, SVG up to 2MB')); ?></p>
                                <input type="file" id="logo-input" form="logo-upload-form" name="logo" accept="image/*" style="display: none;" onchange="document.getElementById('logo-upload-form').submit()">
                            </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Regional Settings')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Timezone')); ?></label>
                            <select name="settings[timezone]" class="form-select">
                                <option value="UTC" <?php echo e(setting('timezone') == 'UTC' ? 'selected' : ''); ?>>UTC</option>
                                <option value="America/New_York" <?php echo e(setting('timezone') == 'America/New_York' ? 'selected' : ''); ?>>America/New York</option>
                                <option value="Europe/London" <?php echo e(setting('timezone') == 'Europe/London' ? 'selected' : ''); ?>>Europe/London</option>
                                <option value="Asia/Dubai" <?php echo e(setting('timezone') == 'Asia/Dubai' ? 'selected' : ''); ?>>Asia/Dubai</option>
                                <option value="Asia/Tokyo" <?php echo e(setting('timezone') == 'Asia/Tokyo' ? 'selected' : ''); ?>>Asia/Tokyo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Date Format')); ?></label>
                            <select name="settings[date_format]" class="form-select">
                                <option value="Y-m-d" <?php echo e(setting('date_format') == 'Y-m-d' ? 'selected' : ''); ?>>2026-01-11</option>
                                <option value="m/d/Y" <?php echo e(setting('date_format') == 'm/d/Y' ? 'selected' : ''); ?>>01/11/2026</option>
                                <option value="d/m/Y" <?php echo e(setting('date_format') == 'd/m/Y' ? 'selected' : ''); ?>>11/01/2026</option>
                                <option value="M d, Y" <?php echo e(setting('date_format') == 'M d, Y' ? 'selected' : ''); ?>>Jan 11, 2026</option>
                                <option value="d M Y" <?php echo e(setting('date_format') == 'd M Y' ? 'selected' : ''); ?>>11 Jan 2026</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Time Format')); ?></label>
                            <select name="settings[time_format]" class="form-select">
                                <option value="H:i" <?php echo e(setting('time_format') == 'H:i' ? 'selected' : ''); ?>><?php echo e(__('24-hour (14:30)')); ?></option>
                                <option value="h:i A" <?php echo e(setting('time_format') == 'h:i A' ? 'selected' : ''); ?>><?php echo e(__('12-hour (02:30 PM)')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Company Info Tab -->
                <div class="tab-pane" id="company-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Company Details')); ?></h3>
                        <p style="color: #64748b; font-size: 13px; margin-bottom: 24px;"><?php echo e(__('This information will appear on invoices and reports.')); ?></p>
                        
                        <?php
                            $companySettings = $settings->get('company', collect());
                        ?>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Company Name')); ?></label>
                            <input type="text" name="settings[company_name]" class="form-input" 
                                   value="<?php echo e($companySettings->where('key', 'company_name')->first()->value ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Address')); ?></label>
                            <textarea name="settings[company_address]" class="form-input" rows="3" style="resize: vertical;"><?php echo e($companySettings->where('key', 'company_address')->first()->value ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Invoicing Information')); ?></h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Tax ID / ICE (Morocco)')); ?></label>
                                <input type="text" name="settings[company_tax_id]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_tax_id')->first()->value ?? ''); ?>"
                                       placeholder="e.g., 001569874000089">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Registry ID / RC (Morocco)')); ?></label>
                                <input type="text" name="settings[company_registry_id]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_registry_id')->first()->value ?? ''); ?>"
                                       placeholder="e.g., 12345">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Professional Tax / Patente')); ?></label>
                                <input type="text" name="settings[company_patente]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_patente')->first()->value ?? ''); ?>"
                                       placeholder="e.g., 45891236">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Fiscal ID / IF')); ?></label>
                                <input type="text" name="settings[company_fiscal_id]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_fiscal_id')->first()->value ?? ''); ?>"
                                       placeholder="e.g., 33221144">
                            </div>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Contact Information')); ?></h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Email Address')); ?></label>
                                <input type="email" name="settings[company_email]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_email')->first()->value ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Phone Number')); ?></label>
                                <input type="text" name="settings[company_phone]" class="form-input" 
                                       value="<?php echo e($companySettings->where('key', 'company_phone')->first()->value ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Website')); ?></label>
                            <input type="url" name="settings[company_website]" class="form-input" 
                                   value="<?php echo e($companySettings->where('key', 'company_website')->first()->value ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Theme Tab -->
                <div class="tab-pane" id="theme-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Colors')); ?></h3>
                        
                        <div class="color-picker-group">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Primary Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[primary_color]" class="color-input" 
                                           value="<?php echo e(setting('primary_color', '#3b82f6')); ?>">
                                    <span class="color-value"><?php echo e(setting('primary_color', '#3b82f6')); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Secondary Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[secondary_color]" class="color-input" 
                                           value="<?php echo e(setting('secondary_color', '#10b981')); ?>">
                                    <span class="color-value"><?php echo e(setting('secondary_color', '#10b981')); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Accent Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[accent_color]" class="color-input" 
                                           value="<?php echo e(setting('accent_color', '#8b5cf6')); ?>">
                                    <span class="color-value"><?php echo e(setting('accent_color', '#8b5cf6')); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Success Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[success_color]" class="color-input" 
                                           value="<?php echo e(setting('success_color', '#10b981')); ?>">
                                    <span class="color-value"><?php echo e(setting('success_color', '#10b981')); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Warning Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[warning_color]" class="color-input" 
                                           value="<?php echo e(setting('warning_color', '#f59e0b')); ?>">
                                    <span class="color-value"><?php echo e(setting('warning_color', '#f59e0b')); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Danger Color')); ?></label>
                                <div class="color-input-wrapper">
                                    <input type="color" name="settings[danger_color]" class="color-input" 
                                           value="<?php echo e(setting('danger_color', '#ef4444')); ?>">
                                    <span class="color-value"><?php echo e(setting('danger_color', '#ef4444')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Typography')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Font Family')); ?></label>
                            <select name="settings[font_family]" class="form-select">
                                <option value="Inter, system-ui, sans-serif" <?php echo e(setting('font_family') == 'Inter, system-ui, sans-serif' ? 'selected' : ''); ?>>Inter</option>
                                <option value="Roboto, sans-serif" <?php echo e(setting('font_family') == 'Roboto, sans-serif' ? 'selected' : ''); ?>>Roboto</option>
                                <option value="Open Sans, sans-serif" <?php echo e(setting('font_family') == 'Open Sans, sans-serif' ? 'selected' : ''); ?>>Open Sans</option>
                                <option value="Poppins, sans-serif" <?php echo e(setting('font_family') == 'Poppins, sans-serif' ? 'selected' : ''); ?>>Poppins</option>
                                <option value="Montserrat, sans-serif" <?php echo e(setting('font_family') == 'Montserrat, sans-serif' ? 'selected' : ''); ?>>Montserrat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Base Font Size (px)')); ?></label>
                            <input type="number" name="settings[font_size_base]" class="form-input" min="12" max="20"
                                   value="<?php echo e(setting('font_size_base', 14)); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Border Radius (px)')); ?></label>
                            <input type="number" name="settings[border_radius]" class="form-input" min="0" max="24"
                                   value="<?php echo e(setting('border_radius', 12)); ?>">
                        </div>
                    </div>
                </div>

                <!-- Localization Tab -->
                <div class="tab-pane" id="localization-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Language & Region')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Language')); ?></label>
                            <select name="settings[language]" class="form-select">
                                <option value="en" <?php echo e(setting('language') == 'en' ? 'selected' : ''); ?>>English</option>
                                <option value="ar" <?php echo e(setting('language') == 'ar' ? 'selected' : ''); ?>>Arabic (العربية)</option>
                                <option value="fr" <?php echo e(setting('language') == 'fr' ? 'selected' : ''); ?>>French (Français)</option>
                                <option value="es" <?php echo e(setting('language') == 'es' ? 'selected' : ''); ?>>Spanish (Español)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Text Direction')); ?></label>
                            <select name="settings[text_direction]" class="form-select">
                                <option value="ltr" <?php echo e(setting('text_direction') == 'ltr' ? 'selected' : ''); ?>>Left to Right (LTR)</option>
                                <option value="rtl" <?php echo e(setting('text_direction') == 'rtl' ? 'selected' : ''); ?>>Right to Left (RTL)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Default Country')); ?></label>
                            <select name="settings[default_country]" class="form-select">
                                <option value="US" <?php echo e(setting('default_country') == 'US' ? 'selected' : ''); ?>><?php echo e(__('United States')); ?></option>
                                <option value="GB" <?php echo e(setting('default_country') == 'GB' ? 'selected' : ''); ?>><?php echo e(__('United Kingdom')); ?></option>
                                <option value="AE" <?php echo e(setting('default_country') == 'AE' ? 'selected' : ''); ?>><?php echo e(__('United Arab Emirates')); ?></option>
                                <option value="SA" <?php echo e(setting('default_country') == 'SA' ? 'selected' : ''); ?>><?php echo e(__('Saudi Arabia')); ?></option>
                                <option value="FR" <?php echo e(setting('default_country') == 'FR' ? 'selected' : ''); ?>><?php echo e(__('France')); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Currency Settings')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Currency Code')); ?></label>
                            <div class="mb-2">
                                <input type="text" id="currency-search" class="form-input" placeholder="<?php echo e(__('Search currency (code or name)...')); ?>" style="margin-bottom: 10px;">
                            </div>
                            <select name="settings[currency_code]" id="currency-select" class="form-select">
                                <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>" 
                                            data-symbol="<?php echo e($data['symbol']); ?>"
                                            <?php echo e(setting('currency_code') == $code ? 'selected' : ''); ?>>
                                        <?php echo e($code); ?> - <?php echo e($data['name']); ?> (<?php echo e($data['symbol']); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Currency Symbol')); ?></label>
                            <input type="text" name="settings[currency_symbol]" class="form-input" maxlength="5"
                                   value="<?php echo e(setting('currency_symbol', '$')); ?>" placeholder="$">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Symbol Position')); ?></label>
                            <select name="settings[currency_position]" class="form-select">
                                <option value="before" <?php echo e(setting('currency_position') == 'before' ? 'selected' : ''); ?>><?php echo e(__('Before amount ($100)')); ?></option>
                                <option value="after" <?php echo e(setting('currency_position') == 'after' ? 'selected' : ''); ?>><?php echo e(__('After amount (100 $)')); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Decimal Places')); ?></label>
                            <input type="number" name="settings[currency_decimals]" class="form-input" min="0" max="4"
                                   value="<?php echo e(setting('currency_decimals', 2)); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Decimal Separator')); ?></label>
                            <input type="text" name="settings[decimal_separator]" class="form-input" maxlength="1"
                                   value="<?php echo e(setting('decimal_separator', '.')); ?>" placeholder=".">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Thousands Separator')); ?></label>
                            <input type="text" name="settings[thousands_separator]" class="form-input" maxlength="1"
                                   value="<?php echo e(setting('thousands_separator', ',')); ?>" placeholder=",">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Tax Configuration')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Tax Rate (%)')); ?></label>
                            <input type="number" name="settings[tax_rate]" class="form-input" min="0" max="100" step="0.01"
                                   value="<?php echo e(setting('tax_rate', 0)); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Tax Label')); ?></label>
                            <input type="text" name="settings[tax_label]" class="form-input"
                                   value="<?php echo e(setting('tax_label', 'Tax')); ?>" placeholder="<?php echo e(__('Tax')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div class="tab-pane" id="advanced-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Pagination Settings')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Items Per Page')); ?></label>
                            <input type="number" name="settings[items_per_page]" class="form-input" min="5" max="100"
                                   value="<?php echo e(setting('items_per_page', 10)); ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Inventory')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Low Stock Threshold')); ?></label>
                            <input type="number" name="settings[low_stock_threshold]" class="form-input" min="0"
                                   value="<?php echo e(setting('low_stock_threshold', 10)); ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Default Values')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Default Order Status')); ?></label>
                            <select name="settings[default_order_status]" class="form-select">
                                <option value="pending" <?php echo e(setting('default_order_status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="processing" <?php echo e(setting('default_order_status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
                                <option value="shipped" <?php echo e(setting('default_order_status') == 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Default Payment Status')); ?></label>
                            <select name="settings[default_payment_status]" class="form-select">
                                <option value="pending" <?php echo e(setting('default_payment_status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="paid" <?php echo e(setting('default_payment_status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                <option value="failed" <?php echo e(setting('default_payment_status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                            </select>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Cache Settings')); ?></h3>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Cache Duration (seconds)')); ?></label>
                            <input type="number" name="settings[cache_duration]" class="form-input" min="0"
                                   value="<?php echo e(setting('cache_duration', 3600)); ?>">
                            <small style="color: #64748b; font-size: 12px;"><?php echo e(__('Set to 0 to disable caching')); ?></small>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Danger Zone')); ?></h3>
                        
                            <button type="submit" form="reset-settings-form" class="btn-danger" onclick="return confirm('<?php echo e(__('Are you sure you want to reset all settings to default values?')); ?>')">
                                <i class="fas fa-undo"></i> <?php echo e(__('Reset All Settings to Default')); ?>

                            </button>
                    </div>
                </div>

                <!-- Social Media Tab -->
                <div class="tab-pane" id="social-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Social Media Profiles')); ?></h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Facebook URL')); ?></label>
                                <input type="text" name="settings[social_facebook]" class="form-input" value="<?php echo e(setting('social_facebook', '#')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Instagram URL')); ?></label>
                                <input type="text" name="settings[social_instagram]" class="form-input" value="<?php echo e(setting('social_instagram', '#')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Twitter/X URL')); ?></label>
                                <input type="text" name="settings[social_twitter]" class="form-input" value="<?php echo e(setting('social_twitter', '#')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('LinkedIn URL')); ?></label>
                                <input type="text" name="settings[social_linkedin]" class="form-input" value="<?php echo e(setting('social_linkedin', '#')); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"><?php echo e(__('WhatsApp (International Format)')); ?></label>
                                <input type="text" name="settings[social_whatsapp]" class="form-input" value="<?php echo e(setting('social_whatsapp')); ?>" placeholder="e.g. 212600000000">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frontend Tab -->
                <div class="tab-pane" id="frontend-tab">
                    <div class="settings-section">
                        <h3 class="section-title"><?php echo e(__('Frontend Experience')); ?></h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Sticky Header')); ?></label>
                                <select name="settings[frontend_sticky_header]" class="form-select">
                                    <option value="1" <?php echo e(setting('frontend_sticky_header') == '1' ? 'selected' : ''); ?>><?php echo e(__('Enabled')); ?></option>
                                    <option value="0" <?php echo e(setting('frontend_sticky_header') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disabled')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Scroll Animations (AOS)')); ?></label>
                                <select name="settings[frontend_enable_animations]" class="form-select">
                                    <option value="1" <?php echo e(setting('frontend_enable_animations') == '1' ? 'selected' : ''); ?>><?php echo e(__('Enabled')); ?></option>
                                    <option value="0" <?php echo e(setting('frontend_enable_animations') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disabled')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('Primary Google Font')); ?></label>
                                <input type="text" name="settings[frontend_primary_font]" class="form-input" value="<?php echo e(setting('frontend_primary_font', 'Inter')); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"><?php echo e(__('Footer Biography / Tagline')); ?></label>
                                <textarea name="settings[frontend_footer_text]" class="form-input" rows="3"><?php echo e(setting('frontend_footer_text')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="window.location.reload()">
                        <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> <?php echo e(__('Save Changes')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Hidden Forms -->
<form id="remove-logo-form" action="<?php echo e(route('settings.logo.remove')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<form id="logo-upload-form" action="<?php echo e(route('settings.logo')); ?>" method="POST" enctype="multipart/form-data" style="display: none;">
    <?php echo csrf_field(); ?>
</form>

<form id="reset-settings-form" action="<?php echo e(route('settings.reset')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.closest('.settings-tab').classList.add('active');

        // Update tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        document.getElementById(tabName + '-tab').classList.add('active');
    }

    // Update color value labels
    document.querySelectorAll('input[type="color"]').forEach(input => {
        input.addEventListener('input', function() {
            const valueSpan = this.nextElementSibling;
            if (valueSpan && valueSpan.classList.contains('color-value')) {
                valueSpan.textContent = this.value;
            }
        });
    });
    // Currency Search & Auto-Symbol
    const currencySearch = document.getElementById('currency-search');
    const currencySelect = document.getElementById('currency-select');
    const currencySymbolInput = document.querySelector('input[name="settings[currency_symbol]"]');

    if (currencySearch && currencySelect) {
        currencySearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = currencySelect.options;
            
            for (let i = 0; i < options.length; i++) {
                const text = options[i].text.toLowerCase();
                const display = text.includes(searchTerm);
                options[i].hidden = !display;
                if (!display && options[i].selected) {
                    // find first visible one? optional
                }
            }
        });

        currencySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const symbol = selectedOption.getAttribute('data-symbol');
            if (symbol && currencySymbolInput) {
                currencySymbolInput.value = symbol;
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/settings/index.blade.php ENDPATH**/ ?>