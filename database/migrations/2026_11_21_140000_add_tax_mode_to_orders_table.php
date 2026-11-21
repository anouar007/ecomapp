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
        if (!Schema::hasColumn('orders', 'tax_mode')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('tax_mode', 10)->default('ht')->after('total')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'tax_mode')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['tax_mode']);
                $table->dropColumn('tax_mode');
            });
        }
    }
};
