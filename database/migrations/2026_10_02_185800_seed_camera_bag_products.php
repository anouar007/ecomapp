<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\CameraBagProductsSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new CameraBagProductsSeeder();
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
