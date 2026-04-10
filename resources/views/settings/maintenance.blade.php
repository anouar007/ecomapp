@extends('layouts.app')

@section('title', __('حالة المتجر والصيانة'))

@section('content')
<div class="settings-container" style="max-width: 800px; margin: 0 auto; direction: rtl;">
    <div class="settings-header" style="margin-bottom: 32px; text-align: right;">
        <h1 class="settings-title" style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">{{ __('حالة المتجر') }}</h1>
        <p class="settings-subtitle" style="color: #64748b; font-size: 15px;">{{ __('إدارة توفر واجهة المتجر ووضع الصيانة') }}</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; background: #d1fae5; color: #065f46; border-right: 4px solid #10b981; text-align: right;">
        <i class="fas fa-check-circle"></i>
        {{ __('تم حفظ الإعدادات بنجاح!') }}
    </div>
    @endif

    <div class="settings-content" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: right;">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="settings-section">
                <h3 class="section-title" style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f1f5f9;">
                    {{ __('إعدادات الصيانة') }}
                </h3>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 24px;">
                    {{ __('عند التفعيل، سيرى الزوار صفحة "قريباً". لا يزال بإمكان المشرفين الوصول إلى لوحة التحكم.') }}
                </p>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ __('حالة المتجر') }}
                    </label>
                    <select name="settings[maintenance_mode]" class="form-select" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: all 0.2s; background-position: left 0.75rem center;">
                        <option value="0" {{ !setting('maintenance_mode') ? 'selected' : '' }}>{{ __('نشط (متوفر أونلاين)') }}</option>
                        <option value="1" {{ setting('maintenance_mode') ? 'selected' : '' }}>{{ __('تحت الصيانة (مغلق مؤقتاً)') }}</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ __('عنوان صفحة الصيانة') }}
                    </label>
                    <input type="text" name="settings[maintenance_title]" class="form-input" 
                           value="{{ setting('maintenance_title', 'سنعود قريباً!') }}"
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px; text-align: right;">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ __('رسالة الصيانة') }}
                    </label>
                    <textarea name="settings[maintenance_message]" class="form-input" rows="4" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px; resize: vertical; text-align: right;">{{ setting('maintenance_message', 'المتجر حالياً تحت الصيانة. يرجى العودة لاحقاً.') }}</textarea>
                </div>
            </div>

            <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-start; margin-top: 32px; padding-top: 24px; border-top: 2px solid #f1f5f9;">
                <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 12px 24px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">
                    {{ __('حفظ حالة المتجر') }}
                </button>
            </div>
        </form>

        <div class="settings-section" style="margin-top: 48px;">
            <h3 class="section-title" style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f1f5f9;">
                {{ __('صورة خلفية صفحة الصيانة') }}
            </h3>
            
            @if(setting('maintenance_image'))
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{{ asset('storage/' . setting('maintenance_image')) }}" alt="{{ __('صورة الصيانة') }}" 
                         style="max-width: 100%; height: 250px; object-fit: cover; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                </div>
            @endif

            <div class="logo-upload-area" onclick="document.getElementById('maintenance-image-input').click()" 
                 style="border: 2px dashed #cbd5e1; border-radius: 16px; padding: 48px; text-align: center; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-image" style="font-size: 48px; color: #3b82f6; margin-bottom: 16px;"></i>
                <p style="margin: 0; color: #1e293b; font-weight: 600;">{{ __('اضغط هنا لتحميل صورة الصيانة') }}</p>
                <p style="margin: 8px 0 0 0; font-size: 13px; color: #64748b;">{{ __('ينصح بصورة عالية الدقة (مثلاً 1920x1080)') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">{{ __('PNG, JPG, WEBP بحد أقصى 5 ميجابايت') }}</p>
                <input type="file" id="maintenance-image-input" form="maintenance-upload-form" name="maintenance_image" 
                       accept="image/*" style="display: none;" onchange="document.getElementById('maintenance-upload-form').submit()">
            </div>
        </div>
    </div>

    <!-- Storage Form -->
    <form id="maintenance-upload-form" action="{{ route('settings.upload-maintenance-image') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
    </form>
</div>

<style>
    .logo-upload-area:hover {
        border-color: #3b82f6;
        background: #f8fafc;
        transform: scale(1.005);
    }
    .form-select {
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: left 0.75rem center;
        background-size: 16px 12px;
    }
</style>
@endsection
