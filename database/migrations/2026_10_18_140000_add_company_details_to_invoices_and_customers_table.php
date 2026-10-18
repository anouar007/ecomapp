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
            $table->string('customer_company')->nullable()->after('customer_name');
            $table->string('customer_rc')->nullable()->after('ice');
            $table->string('customer_if')->nullable()->after('customer_rc');
            $table->string('customer_patente')->nullable()->after('customer_if');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->string('rc')->nullable()->after('ice');
            $table->string('if_number')->nullable()->after('rc');
            $table->string('patente')->nullable()->after('if_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['customer_company', 'customer_rc', 'customer_if', 'customer_patente']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'rc', 'if_number', 'patente']);
        });
    }
};
