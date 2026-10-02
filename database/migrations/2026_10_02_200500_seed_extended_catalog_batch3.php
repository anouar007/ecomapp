<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ExtendedCatalogBatch3Seeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new ExtendedCatalogBatch3Seeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
