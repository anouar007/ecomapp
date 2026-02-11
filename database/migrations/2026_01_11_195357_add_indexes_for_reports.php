<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check and add indexes only if they don't exist
            $indexes = $this->getIndexes('orders');
            
            if (!in_array('orders_created_at_index', $indexes)) {
                $table->index('created_at');
            }
            if (!in_array('orders_status_payment_status_index', $indexes)) {
                $table->index(['status', 'payment_status']);
            }
            if (!in_array('orders_created_at_total_index', $indexes)) {
                $table->index(['created_at', 'total']);
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            $indexes = $this->getIndexes('order_items');
            
            if (!in_array('order_items_product_id_index', $indexes)) {
                $table->index('product_id');
            }
            if (!in_array('order_items_product_id_quantity_index', $indexes)) {
                $table->index(['product_id', 'quantity']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $indexes = $this->getIndexes('products');
            
            if (!in_array('products_category_id_index', $indexes) && Schema::hasColumn('products', 'category_id')) {
                $table->index('category_id');
            }
            if (!in_array('products_status_stock_index', $indexes)) {
                $table->index(['status', 'stock']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $indexes = $this->getIndexes('orders');
            
            if (in_array('orders_created_at_index', $indexes)) {
                $table->dropIndex(['created_at']);
            }
            if (in_array('orders_status_payment_status_index', $indexes)) {
                $table->dropIndex(['status', 'payment_status']);
            }
            if (in_array('orders_created_at_total_index', $indexes)) {
                $table->dropIndex(['created_at', 'total']);
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            $indexes = $this->getIndexes('order_items');
            
            if (in_array('order_items_product_id_index', $indexes)) {
                $table->dropIndex(['product_id']);
            }
            if (in_array('order_items_product_id_quantity_index', $indexes)) {
                $table->dropIndex(['product_id', 'quantity']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $indexes = $this->getIndexes('products');
            
            if (in_array('products_status_stock_index', $indexes)) {
                $table->dropIndex(['status', 'stock']);
            }
        });
    }

    /**
     * Get existing indexes for a table
     */
    private function getIndexes(string $table): array
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        return collect($indexes)->pluck('Key_name')->unique()->toArray();
    }
};
