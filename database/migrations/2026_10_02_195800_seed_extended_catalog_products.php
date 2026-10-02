<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ExtendedCatalogProductsSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new ExtendedCatalogProductsSeeder();
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
