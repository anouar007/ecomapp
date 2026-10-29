<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('product_tags') && !Schema::hasColumn('product_tags', 'is_discount_tag')) {
            Schema::table('product_tags', function (Blueprint $table) {
                $table->boolean('is_discount_tag')->default(false)->after('slug');
            });
        }

        // Seed the customizable Auto-Calculated Discount Percentage Tag if it doesn't exist
        if (Schema::hasTable('product_tags') && DB::table('product_tags')->where('is_discount_tag', true)->count() === 0) {
            $now = now();
            DB::table('product_tags')->insert([
                'name' => '-{percent}%',
                'slug' => 'auto-discount-percentage',
                'is_discount_tag' => true,
                'icon' => null,
                'style_type' => 'gradient',
                'bg_color' => '#dc2626',
                'bg_gradient' => '#991b1b',
                'text_color' => '#ffffff',
                'border_color' => null,
                'shape' => 'rounded',
                'position' => 'top-left',
                'size_scale' => 100,
                'font_weight' => '800',
                'uppercase' => true,
                'pulse_animation' => false,
                'has_shadow' => false,
                'status' => 'active',
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_tags') && Schema::hasColumn('product_tags', 'is_discount_tag')) {
            DB::table('product_tags')->where('is_discount_tag', true)->delete();
            Schema::table('product_tags', function (Blueprint $table) {
                $table->dropColumn('is_discount_tag');
            });
        }
    }
};

