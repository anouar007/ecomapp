<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ExtendedCatalogBatch7Seeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new ExtendedCatalogBatch7Seeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback for seeder data
    }
};
?>
