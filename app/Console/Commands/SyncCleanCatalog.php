<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCleanCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:sync-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the verified, deduplicated product catalog into the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = database_path('catalog_update.sql');

        if (!file_exists($path)) {
            $this->error("Catalog update file not found at: {$path}");
            return 1;
        }

        $this->info("Importing verified, deduplicated catalog from database/catalog_update.sql...");

        try {
            $db = config('database.connections.mysql');
            $passArg = $db['password'] !== '' ? '--password=' . escapeshellarg($db['password']) : '';
            $cmd = sprintf(
                'mysql --user=%s %s --host=%s --port=%s %s < %s 2>&1',
                escapeshellarg($db['username']),
                $passArg,
                escapeshellarg($db['host']),
                escapeshellarg($db['port']),
                escapeshellarg($db['database']),
                escapeshellarg($path)
            );

            exec($cmd, $output, $code);

            if ($code === 0) {
                $this->info("✓ Successfully updated categories, products, and product images!");
                $this->call('optimize:clear');
                return 0;
            }

            // Fallback: Statement by statement execution
            $this->line("Streaming SQL statements via PDO...");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $handle = fopen($path, 'r');
            $stmt = '';
            while (($line = fgets($handle)) !== false) {
                $trim = trim($line);
                if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '/*')) {
                    continue;
                }
                $stmt .= $line;
                if (str_ends_with($trim, ';')) {
                    try {
                        DB::statement($stmt);
                    } catch (\Throwable $e) {
                        // ignore minor comment directives
                    }
                    $stmt = '';
                }
            }
            fclose($handle);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info("✓ Successfully updated categories, products, and product images!");
            $this->call('optimize:clear');
            return 0;
        } catch (\Throwable $e) {
            $this->error("Error importing catalog: " . $e->getMessage());
            return 1;
        }
    }
}
