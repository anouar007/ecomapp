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
        if (!Schema::hasColumn('orders', 'delivery_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('delivery_type', 30)->nullable()->index()->after('tax_mode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'delivery_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['delivery_type']);
                $table->dropColumn('delivery_type');
            });
        }
    }
};
