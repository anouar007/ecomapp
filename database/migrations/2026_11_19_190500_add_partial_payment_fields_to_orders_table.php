<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'partial', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->nullable()->after('total');
            }
            if (!Schema::hasColumn('orders', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_due_date')) {
                $table->date('payment_due_date')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_due_date')) {
                $table->dropColumn('payment_due_date');
            }
            if (Schema::hasColumn('orders', 'remaining_amount')) {
                $table->dropColumn('remaining_amount');
            }
            if (Schema::hasColumn('orders', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE orders SET payment_status = 'pending' WHERE payment_status = 'partial'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
