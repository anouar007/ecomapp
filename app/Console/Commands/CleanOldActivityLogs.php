<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanOldActivityLogs extends Command
{
    protected $signature = 'logs:clean {days=90}';
    protected $description = 'Clean activity logs older than specified days';

    public function handle()
    {
        $days = $this->argument('days');
        
        $count = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$count} activity logs older than {$days} days.");
        
        return 0;
    }
}
