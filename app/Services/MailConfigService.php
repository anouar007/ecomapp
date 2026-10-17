<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MailConfigService
{
    /**
     * Apply mail configuration dynamically from database settings.
     */
    public static function applyConfig(): bool
    {
        try {
            $host = Setting::get('mail_host') ?: config('mail.mailers.smtp.host', 'smtp.hostinger.com');
            $port = (int)(Setting::get('mail_port') ?: config('mail.mailers.smtp.port', 465));
            $username = Setting::get('mail_username') ?: config('mail.mailers.smtp.username');
            $password = Setting::get('mail_password') ?: config('mail.mailers.smtp.password');
            $encryption = Setting::get('mail_encryption') ?: config('mail.mailers.smtp.encryption', 'ssl');
            $fromAddress = Setting::get('mail_from_address') ?: ($username ?: config('mail.from.address', 'contact@winashop.ma'));
            $fromName = Setting::get('mail_from_name') ?: Setting::get('app_name', config('mail.from.name', 'Wina Shop'));

            // Always configure smtp transport (unless Mail is currently faked in tests)
            $isFaked = class_exists(\Illuminate\Support\Testing\Fakes\MailFake::class) 
                && (\Illuminate\Support\Facades\Mail::getFacadeRoot() instanceof \Illuminate\Support\Testing\Fakes\MailFake);
            
            if (!$isFaked) {
                Config::set('mail.default', !empty($username) ? 'smtp' : config('mail.default', 'log'));
            }
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', $port);
            Config::set('mail.mailers.smtp.encryption', ($encryption === 'none' || empty($encryption)) ? null : $encryption);
            Config::set('mail.mailers.smtp.username', $username);
            Config::set('mail.mailers.smtp.password', $password);
            Config::set('mail.mailers.smtp.timeout', 4);

            if (!empty($fromAddress)) {
                Config::set('mail.from.address', $fromAddress);
                Config::set('mail.from.name', $fromName);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to apply dynamic mail config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the list of recipient emails for new order notifications.
     */
    public static function getOrderNotificationRecipients(): array
    {
        $raw = Setting::get('order_notification_email') 
            ?: Setting::get('contact_email') 
            ?: Setting::get('company_email') 
            ?: config('mail.from.address');

        if (empty($raw)) {
            return [];
        }

        // Split by comma or semicolon
        $emails = preg_split('/[,;]+/', $raw);
        $clean = [];
        foreach ($emails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $clean[] = $email;
            }
        }

        return array_unique($clean);
    }
}
