<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // General Settings
            ['key' => 'app_name', 'value' => 'E-commerce', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Modern E-commerce Platform', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general'],
            ['key' => 'date_format', 'value' => 'M d, Y', 'type' => 'string', 'group' => 'general'],
            ['key' => 'time_format', 'value' => 'H:i', 'type' => 'string', 'group' => 'general'],
            ['key' => 'items_per_page', 'value' => '10', 'type' => 'integer', 'group' => 'general'],
            
            // Theme Settings
            ['key' => 'primary_color', 'value' => '#3b82f6', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'secondary_color', 'value' => '#10b981', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'accent_color', 'value' => '#8b5cf6', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'danger_color', 'value' => '#ef4444', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'warning_color', 'value' => '#f59e0b', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'success_color', 'value' => '#10b981', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'font_family', 'value' => 'Inter, system-ui, sans-serif', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'font_size_base', 'value' => '14', 'type' => 'integer', 'group' => 'theme'],
            ['key' => 'border_radius', 'value' => '12', 'type' => 'integer', 'group' => 'theme'],
            ['key' => 'sidebar_bg', 'value' => 'linear-gradient(180deg, #1e293b 0%, #0f172a 100%)', 'type' => 'string', 'group' => 'theme'],
            
            // Localization Settings
            ['key' => 'language', 'value' => 'en', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'text_direction', 'value' => 'ltr', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_code', 'value' => 'USD', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_position', 'value' => 'before', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_decimals', 'value' => '2', 'type' => 'integer', 'group' => 'localization'],
            ['key' => 'decimal_separator', 'value' => '.', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'thousands_separator', 'value' => ',', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'default_country', 'value' => 'US', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'tax_rate', 'value' => '0', 'type' => 'float', 'group' => 'localization'],
            ['key' => 'tax_label', 'value' => 'Tax', 'type' => 'string', 'group' => 'localization'],
            
            // Advanced Settings
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'integer', 'group' => 'advanced'],
            ['key' => 'default_order_status', 'value' => 'pending', 'type' => 'string', 'group' => 'advanced'],
            ['key' => 'default_payment_status', 'value' => 'pending', 'type' => 'string', 'group' => 'advanced'],
            ['key' => 'enable_reports', 'value' => '1', 'type' => 'boolean', 'group' => 'advanced'],
            ['key' => 'enable_analytics', 'value' => '1', 'type' => 'boolean', 'group' => 'advanced'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'advanced'],
            ['key' => 'enable_cache', 'value' => '1', 'type' => 'boolean', 'group' => 'advanced'],
            ['key' => 'cache_duration', 'value' => '3600', 'type' => 'integer', 'group' => 'advanced'],

            // Company Information
            ['key' => 'company_name', 'value' => 'My Company Name', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_address', 'value' => '123 Business Street, City, Country', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'info@company.com', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '+1234567890', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_tax_id', 'value' => '', 'type' => 'string', 'group' => 'company'], // ICE
            ['key' => 'company_registry_id', 'value' => '', 'type' => 'string', 'group' => 'company'], // RC
            ['key' => 'company_patente', 'value' => '', 'type' => 'string', 'group' => 'company'], // Professional Tax
            ['key' => 'company_fiscal_id', 'value' => '', 'type' => 'string', 'group' => 'company'], // IF
            ['key' => 'company_website', 'value' => 'https://example.com', 'type' => 'string', 'group' => 'company'],

            // Social Media Settings
            ['key' => 'social_facebook', 'value' => '#', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => '#', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '#', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => '#', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_whatsapp', 'value' => '', 'type' => 'string', 'group' => 'social'],

            // Frontend Appearance
            ['key' => 'frontend_sticky_header', 'value' => '1', 'type' => 'boolean', 'group' => 'frontend'],
            ['key' => 'frontend_footer_text', 'value' => 'Built with Speed CMS - Your ultimate commerce solution.', 'type' => 'string', 'group' => 'frontend'],
            ['key' => 'frontend_primary_font', 'value' => 'Inter', 'type' => 'string', 'group' => 'frontend'],
            ['key' => 'frontend_enable_animations', 'value' => '1', 'type' => 'boolean', 'group' => 'frontend'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
