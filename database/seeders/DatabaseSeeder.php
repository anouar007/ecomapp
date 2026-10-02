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
            BatteryChargerProductsSeeder::class,
            MemoryCardProductsSeeder::class,
            ExtendedCatalogProductsSeeder::class,
            ExtendedCatalogBatch2Seeder::class,
            ExtendedCatalogBatch3Seeder::class,
            ExtendedCatalogBatch4Seeder::class,
            ExtendedCatalogBatch5Seeder::class,
        ]);
    }
}
