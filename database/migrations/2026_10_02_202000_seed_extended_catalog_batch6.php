<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ExtendedCatalogBatch6Seeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new ExtendedCatalogBatch6Seeder();
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
