<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ExtendedCatalogBatch4Seeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new ExtendedCatalogBatch4Seeder();
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
