<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }

    public function up(): void
    {
        // 1. Products table performance indexes
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'products_price_index')) {
                $table->index('price', 'products_price_index');
            }
            if (!$this->indexExists('products', 'products_created_at_index')) {
                $table->index('created_at', 'products_created_at_index');
            }
            if (!$this->indexExists('products', 'products_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'products_status_created_at_index');
            }
            if (!$this->indexExists('products', 'products_status_price_index')) {
                $table->index(['status', 'price'], 'products_status_price_index');
            }
        });

        // 2. Product Images composite index for instant primary lookup
        Schema::table('product_images', function (Blueprint $table) {
            if (!$this->indexExists('product_images', 'product_images_pid_primary_index')) {
                $table->index(['product_id', 'is_primary'], 'product_images_pid_primary_index');
            }
        });

        // 3. Banners fast position/status lookup
        Schema::table('banners', function (Blueprint $table) {
            if (!$this->indexExists('banners', 'banners_status_pos_sort_index')) {
                $table->index(['status', 'position', 'sort_order'], 'banners_status_pos_sort_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if ($this->indexExists('products', 'products_price_index')) {
                $table->dropIndex('products_price_index');
            }
            if ($this->indexExists('products', 'products_created_at_index')) {
                $table->dropIndex('products_created_at_index');
            }
            if ($this->indexExists('products', 'products_status_created_at_index')) {
                $table->dropIndex('products_status_created_at_index');
            }
            if ($this->indexExists('products', 'products_status_price_index')) {
                $table->dropIndex('products_status_price_index');
            }
        });

        Schema::table('product_images', function (Blueprint $table) {
            if ($this->indexExists('product_images', 'product_images_pid_primary_index')) {
                $table->dropIndex('product_images_pid_primary_index');
            }
        });

        Schema::table('banners', function (Blueprint $table) {
            if ($this->indexExists('banners', 'banners_status_pos_sort_index')) {
                $table->dropIndex('banners_status_pos_sort_index');
            }
        });
    }
};
