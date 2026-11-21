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
        if (!Schema::hasColumn('orders', 'payment_attachments')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->json('payment_attachments')->nullable()->after('payment_due_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'payment_attachments')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('payment_attachments');
            });
        }
    }
};
