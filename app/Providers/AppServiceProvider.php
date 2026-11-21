<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);

        // Ensure $errors is ALWAYS an instance of ViewErrorBag in all views
        // to prevent fatal "Call to a member function getBag() on null" on @error directives
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $data = $view->getData();
            if (!isset($data['errors']) || !($data['errors'] instanceof \Illuminate\Support\ViewErrorBag)) {
                $sessionErrors = null;
                try {
                    if (request()->hasSession()) {
                        $sessionErrors = request()->session()->get('errors');
                    }
                } catch (\Throwable $e) {
                    // Session not available
                }
                $view->with('errors', ($sessionErrors instanceof \Illuminate\Support\ViewErrorBag) ? $sessionErrors : new \Illuminate\Support\ViewErrorBag);
            }
        });

        if ($this->app->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Canonical host & path safety: always force https://winashop.com (non-www, no /public) in production
        if (!$this->app->environment('local')) {
            try {
                $reqHost = request()->getHost();
            } catch (\Throwable $e) {
                $reqHost = '';
            }
            if (str_contains($reqHost, 'winashop.com') || str_contains((string) config('app.url'), 'winashop.com')) {
                config(['app.url' => 'https://winashop.com']);
                \Illuminate\Support\Facades\URL::forceRootUrl('https://winashop.com');
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        // Implicitly grant "Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

        // Automatically track user last login timestamp & IP address on every successful login
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            if ($event->user instanceof \App\Models\User) {
                try {
                    $event->user->forceFill([
                        'last_login_at' => now(),
                        'last_login_ip' => request()->ip(),
                    ])->saveQuietly();
                } catch (\Throwable $e) {}
            }
        });

        // Hard-block all Notification mail channel dispatches
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Notifications\Events\NotificationSending::class, function ($event) {
            if (($event->channel ?? null) === 'mail') {
                return false;
            }
        });

        // Strict transport-level firewall: admin/store addresses only ever receive New Website Order notifications
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Mail\Events\MessageSending::class, function (\Illuminate\Mail\Events\MessageSending $event) {
            if (!empty($event->data['__laravel_notification'])) {
                return false;
            }

            $subject = mb_strtolower((string) $event->message->getSubject());
            foreach (['low stock', 'stock alert', 'rupture de stock', 'stock faible', 'daily summary'] as $blockedKeyword) {
                if (str_contains($subject, $blockedKeyword)) {
                    return false;
                }
            }

            $orderObj = $event->data['order'] ?? null;
            if ($orderObj instanceof \App\Models\Order && $orderObj->isPosOrder()) {
                return false;
            }

            $protectedEmails = [];
            $collectEmails = function ($raw) use (&$protectedEmails) {
                foreach (preg_split('/[,;]+/', (string) $raw) as $candidate) {
                    $clean = mb_strtolower(trim($candidate));
                    if ($clean !== '' && filter_var($clean, FILTER_VALIDATE_EMAIL)) {
                        $protectedEmails[$clean] = true;
                    }
                }
            };

            try {
                foreach (\App\Services\MailConfigService::getOrderNotificationRecipients() as $rec) {
                    $collectEmails($rec);
                }
                $collectEmails(\App\Models\Setting::get('contact_email'));
                $collectEmails(\App\Models\Setting::get('company_email'));
                $collectEmails(config('mail.from.address'));

                foreach (\App\Models\User::whereNotNull('email')->pluck('email') as $userEmail) {
                    $collectEmails($userEmail);
                }
                foreach (\App\Models\NotificationSetting::whereNotNull('notification_email')->pluck('notification_email') as $notifEmail) {
                    $collectEmails($notifEmail);
                }
            } catch (\Throwable $e) {
                // Fail closed only on what we could resolve during early boot/migrations
            }

            $allRecipients = array_merge(
                $event->message->getTo() ?? [],
                $event->message->getCc() ?? [],
                $event->message->getBcc() ?? []
            );

            $targetsProtectedAdmin = false;
            foreach ($allRecipients as $address) {
                $addr = mb_strtolower(trim((string) $address->getAddress()));
                if ($addr !== '' && isset($protectedEmails[$addr])) {
                    $targetsProtectedAdmin = true;
                    break;
                }
            }

            if ($targetsProtectedAdmin) {
                $typeHeader = $event->message->getHeaders()->get('X-Wina-Mail-Type');
                $mailType = $typeHeader && method_exists($typeHeader, 'getBodyAsString')
                    ? mb_strtolower(trim((string) $typeHeader->getBodyAsString()))
                    : '';

                $isWebsiteNewOrder = $mailType === 'website-new-order'
                    || str_contains($subject, 'nouvelle commande #')
                    || str_contains($subject, 'new order');

                if (!$isWebsiteNewOrder) {
                    return false;
                }
            }
        });

        try {
            // Override App Name
            if ($appName = \App\Models\Setting::get('app_name')) {
                config(['app.name' => $appName]);
            }

            // Override Timezone
            if ($timezone = \App\Models\Setting::get('timezone')) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }

            // Override Locale
            if ($locale = \App\Models\Setting::get('language')) {
                config(['app.locale' => $locale]);
                \App::setLocale($locale);
            }

            // Dynamically configure SMTP mail settings from database
            \App\Services\MailConfigService::applyConfig();
        } catch (\Throwable $e) {
            // Fail silently during early setup/migrations
        }
    }
}
