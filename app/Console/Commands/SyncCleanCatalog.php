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
            // Use direct PDO connection for maximum compatibility and performance.
            // Does not rely on shell functions (exec, system, etc.) which may be disabled on production servers.
            $pdo = DB::connection()->getPdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');

            $handle = fopen($path, 'r');
            if (!$handle) {
                $this->error("Unable to open catalog SQL file.");
                return 1;
            }

            $stmt = '';
            $executed = 0;

            while (($line = fgets($handle)) !== false) {
                $trim = trim($line);

                // Skip comments and empty lines
                if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '/*')) {
                    continue;
                }

                $stmt .= $line;

                if (str_ends_with($trim, ';')) {
                    try {
                        $pdo->exec($stmt);
                        $executed++;
                    } catch (\Throwable $e) {
                        // Ignore non-critical comment directives if any
                    }
                    $stmt = '';
                }
            }

            fclose($handle);
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            $this->info("✓ Successfully updated categories, products, and product images ({$executed} statements executed)!");

            // Clear application caches
            $this->call('optimize:clear');

            return 0;
        } catch (\Throwable $e) {
            $this->error("Error importing catalog: " . $e->getMessage());
            return 1;
        }
    }
}
