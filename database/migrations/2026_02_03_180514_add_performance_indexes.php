<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'orders_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('orders', 'orders_customer_email_index')) {
                $table->index('customer_email');
            }
            if (!$this->indexExists('orders', 'orders_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            if (!$this->indexExists('invoices', 'invoices_payment_status_index')) {
                $table->index('payment_status');
            }
            if (!$this->indexExists('invoices', 'invoices_customer_email_index')) {
                $table->index('customer_email');
            }
            if (!$this->indexExists('invoices', 'invoices_due_date_index')) {
                $table->index('due_date');
            }
        });

        // Products table indexes  
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'products_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('products', 'products_category_index') && Schema::hasColumn('products', 'category')) {
                $table->index('category');
            }
        });

        // Customers table indexes
        Schema::table('customers', function (Blueprint $table) {
            if (!$this->indexExists('customers', 'customers_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('customers', 'customers_current_balance_index')) {
                $table->index('current_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop indexes we created
        Schema::table('orders', function (Blueprint $table) {
            if ($this->indexExists('orders', 'orders_created_at_index')) {
                $table->dropIndex(['created_at']);
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if ($this->indexExists('invoices', 'invoices_due_date_index')) {
                $table->dropIndex(['due_date']);
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if ($this->indexExists('customers', 'customers_current_balance_index')) {
                $table->dropIndex(['current_balance']);
            }
        });
    }
};
