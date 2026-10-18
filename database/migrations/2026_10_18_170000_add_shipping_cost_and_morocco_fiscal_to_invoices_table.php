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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('invoices', 'customer_company')) {
                $table->string('customer_company')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('invoices', 'customer_rc')) {
                $table->string('customer_rc')->nullable()->after('ice');
            }
            if (!Schema::hasColumn('invoices', 'customer_if')) {
                $table->string('customer_if')->nullable()->after('customer_rc');
            }
            if (!Schema::hasColumn('invoices', 'customer_patente')) {
                $table->string('customer_patente')->nullable()->after('customer_if');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $cols = ['shipping_cost', 'customer_company', 'customer_rc', 'customer_if', 'customer_patente'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('invoices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
