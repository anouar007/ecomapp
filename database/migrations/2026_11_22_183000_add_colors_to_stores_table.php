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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'badge_bg_color')) {
                $table->string('badge_bg_color', 20)->nullable()->after('type');
            }
            if (!Schema::hasColumn('stores', 'badge_text_color')) {
                $table->string('badge_text_color', 20)->nullable()->after('badge_bg_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('stores', 'badge_text_color')) {
                $dropCols[] = 'badge_text_color';
            }
            if (Schema::hasColumn('stores', 'badge_bg_color')) {
                $dropCols[] = 'badge_bg_color';
            }
            if ($dropCols !== []) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
