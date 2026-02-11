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
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_limit', 10, 2)->default(0)->after('status');
            // We can add current_balance too, but defining it as calculated is often safer.
            // However, for performance on searching "Debtors", a stored balance is better.
            // Let's add it but ensure we maintain it.
            $table->decimal('current_balance', 10, 2)->default(0)->after('credit_limit'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'current_balance']);
        });
    }
};
