<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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

        try {
            // Only attempt to load settings if the table exists to prevent migration errors
            if (\Schema::hasTable('settings')) {
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
            }
        } catch (\Exception $e) {
            // Fail silently during early setup/migrations
        }
    }
}
