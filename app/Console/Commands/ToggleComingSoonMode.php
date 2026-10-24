<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ToggleComingSoonMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coming-soon 
                            {action=status : enable, disable, toggle, or status}
                            {--secret= : Secret token for preview bypass (default: wina2026)}
                            {--launch= : Projected launch date (e.g. "2026-11-05 10:00:00")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable Coming Soon mode for public visitors while keeping admin access';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = strtolower((string) $this->argument('action'));
        $current = (bool) setting('coming_soon_enabled', false);

        switch ($action) {
            case 'enable':
            case 'on':
            case '1':
            case 'true':
                $this->enableComingSoon();
                break;

            case 'disable':
            case 'off':
            case '0':
            case 'false':
                $this->disableComingSoon();
                break;

            case 'toggle':
                if ($current) {
                    $this->disableComingSoon();
                } else {
                    $this->enableComingSoon();
                }
                break;

            case 'status':
            default:
                $this->displayStatus();
                break;
        }

        return 0;
    }

    protected function enableComingSoon(): void
    {
        Setting::set('coming_soon_enabled', '1', 'boolean', 'general');

        if ($this->option('secret')) {
            Setting::set('coming_soon_secret', $this->option('secret'), 'string', 'general');
        }

        if ($this->option('launch')) {
            Setting::set('coming_soon_launch_date', $this->option('launch'), 'string', 'general');
        }

        Cache::flush();

        $this->newLine();
        $this->info("==================================================================");
        $this->info("      🔒 COMING SOON MODE IS NOW ENABLED (ACTIVE)");
        $this->info("==================================================================");
        $this->line("• Public visitors will now see the high-end Coming Soon page.");
        $this->line("• Admin panel and login routes (/login, /dashboard) remain 100% accessible.");
        $secret = (string) setting('coming_soon_secret', 'wina2026');
        $this->line("• Secret preview URL for team testing: " . url("/?preview={$secret}"));
        $this->info("==================================================================");
        $this->newLine();
    }

    protected function disableComingSoon(): void
    {
        Setting::set('coming_soon_enabled', '0', 'boolean', 'general');
        Cache::flush();

        $this->newLine();
        $this->info("==================================================================");
        $this->info("      🟢 COMING SOON MODE IS NOW DISABLED (SITE IS LIVE)");
        $this->info("==================================================================");
        $this->line("• The public website is now LIVE and accessible to all visitors!");
        $this->info("==================================================================");
        $this->newLine();
    }

    protected function displayStatus(): void
    {
        $current = (bool) setting('coming_soon_enabled', false);
        $secret = (string) setting('coming_soon_secret', 'wina2026');
        $launch = (string) setting('coming_soon_launch_date', 'Non spécifiée');

        $this->newLine();
        $this->info("==================================================================");
        $this->info("                  COMING SOON MODE STATUS");
        $this->info("==================================================================");
        $this->line("Current Status:    " . ($current ? "🔴 ACTIVE (Coming Soon page displayed to public)" : "🟢 INACTIVE (Website is Live)"));
        $this->line("Secret Bypass:     {$secret}");
        $this->line("Preview URL:       " . url("/?preview={$secret}"));
        $this->line("Target Launch:     {$launch}");
        $this->newLine();
        $this->comment("To enable:  php artisan coming-soon enable");
        $this->comment("To disable: php artisan coming-soon disable");
        $this->comment("To toggle:  php artisan coming-soon toggle");
        $this->info("==================================================================");
        $this->newLine();
    }
}
