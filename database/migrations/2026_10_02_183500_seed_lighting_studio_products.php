<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\LightingStudioProductsSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new LightingStudioProductsSeeder();
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
