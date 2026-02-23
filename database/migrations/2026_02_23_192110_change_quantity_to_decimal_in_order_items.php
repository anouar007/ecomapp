<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change order_items.quantity from integer to decimal(10,3)
     * to support POS orders with fractional quantities (e.g. 1.5 m, 2.75 kg).
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
    }

    /**
     * Rollback to integer (existing values will be truncated).
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
