<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            StoreCategoriesSeeder::class,
            CameraProductsSeeder::class,
            LensProductsSeeder::class,
            LightingStudioProductsSeeder::class,
            AudioPodcastProductsSeeder::class,
            CameraBagProductsSeeder::class,
            TripodSupportProductsSeeder::class,
        ]);
    }
}
