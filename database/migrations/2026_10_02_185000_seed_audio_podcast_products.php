<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\AudioPodcastProductsSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new AudioPodcastProductsSeeder();
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
