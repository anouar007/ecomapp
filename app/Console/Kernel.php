<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdateExpiredCoupons::class,
        Commands\CleanOldActivityLogs::class,
        Commands\SendDailySummary::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Update expired coupons daily
        $schedule->command('coupons:update-expired')->daily();

        // Clean old activity logs weekly
        $schedule->command('logs:clean 90')->weekly();

        // Send daily summary at 8 AM
        $schedule->command('summary:daily')->dailyAt('08:00');

        // Database backup (if configured)
        $schedule->command('backup:run')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
