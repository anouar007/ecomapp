<?php

namespace Database\Seeders;

use App\Models\Product;
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
        ]);

        // Protect existing production/store catalogs from being overwritten by legacy placeholder seeders.
        $hasExistingProducts = Product::count() > 0;
        $forceLegacySeed = filter_var(env('FORCE_LEGACY_CATALOG_SEED', false), FILTER_VALIDATE_BOOLEAN);

        if ($hasExistingProducts && !$forceLegacySeed) {
            $this->command?->warn('Skipped legacy catalog product seeders because products already exist. Set FORCE_LEGACY_CATALOG_SEED=true only on an intentionally fresh catalog setup.');
            return;
        }

        $this->call([
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
            ExtendedCatalogBatch6Seeder::class,
            ExtendedCatalogBatch7Seeder::class,
            ExtendedCatalogBatch8Seeder::class,
        ]);
    }
}
