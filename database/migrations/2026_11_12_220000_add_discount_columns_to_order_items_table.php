<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_price');
            }
            if (!Schema::hasColumn('order_items', 'discount_type')) {
                $table->string('discount_type', 20)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('order_items', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['discount_value', 'discount_type', 'discount_amount', 'original_price'] as $col) {
                if (Schema::hasColumn('order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

