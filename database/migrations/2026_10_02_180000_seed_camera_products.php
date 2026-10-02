<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\CameraProductsSeeder;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new CameraProductsSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep products intact or remove Camera products if rollback requested
    }
};
