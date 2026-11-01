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
        Schema::table('products', function (Blueprint $table) {
            $table->string('custom_stock_button_text', 100)->nullable()->after('custom_stock_tag');
            $table->string('custom_tag_color', 30)->nullable()->after('custom_stock_button_text');
            $table->string('custom_tag_bg', 50)->nullable()->after('custom_tag_color');
            $table->string('custom_tag_border', 50)->nullable()->after('custom_tag_bg');
            $table->string('custom_btn_color', 30)->nullable()->after('custom_tag_border');
            $table->string('custom_btn_bg', 120)->nullable()->after('custom_btn_color');
            $table->string('custom_btn_border', 50)->nullable()->after('custom_btn_bg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'custom_stock_button_text',
                'custom_tag_color',
                'custom_tag_bg',
                'custom_tag_border',
                'custom_btn_color',
                'custom_btn_bg',
                'custom_btn_border',
            ]);
        });
    }
};

