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

        if ($this->app->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
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
