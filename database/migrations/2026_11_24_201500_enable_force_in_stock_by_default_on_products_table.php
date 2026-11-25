<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'force_in_stock')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('force_in_stock')->default(true)->change();
        });

        DB::table('products')->update(['force_in_stock' => true]);

        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');

        $fileCache = Cache::store('file');
        foreach ([
            'frontend_nav_categories',
            'shop_catalog_categories',
            'total_active_products_count',
            'all_active_product_names_map_v1',
            'shop_instock_pool_v1',
            'home_featured_products_v1',
            'home_new_arrival_products_v1',
            'home_occasion_products_v1',
        ] as $cacheKey) {
            $fileCache->forget($cacheKey);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'force_in_stock')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('force_in_stock')->default(false)->change();
        });
    }
};
