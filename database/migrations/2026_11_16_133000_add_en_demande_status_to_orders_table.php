<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending','en_demande','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('orders')->where('status', 'en_demande')->update(['status' => 'pending']);
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
