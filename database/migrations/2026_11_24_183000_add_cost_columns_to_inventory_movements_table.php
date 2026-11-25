<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_movements', 'unit_cost')) {
                $table->decimal('unit_cost', 12, 2)->nullable()->after('stock_after');
            }
            if (!Schema::hasColumn('inventory_movements', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('unit_cost');
            }
            if (!Schema::hasColumn('inventory_movements', 'cost_before')) {
                $table->decimal('cost_before', 12, 2)->nullable()->after('total_cost');
            }
            if (!Schema::hasColumn('inventory_movements', 'cost_after')) {
                $table->decimal('cost_after', 12, 2)->nullable()->after('cost_before');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            $dropCols = array_values(array_filter([
                'unit_cost',
                'total_cost',
                'cost_before',
                'cost_after',
            ], fn (string $col) => Schema::hasColumn('inventory_movements', $col)));

            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
