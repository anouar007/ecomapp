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
            $reqHost = request()->getHost();
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
