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
        if (!Schema::hasTable('product_tags')) {
            Schema::create('product_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->nullable()->comment('FontAwesome class, e.g., fas fa-fire');
                $table->string('style_type')->default('solid')->comment('solid, gradient, outline, soft, glass');
                $table->string('bg_color', 30)->default('#dc2626');
                $table->string('bg_gradient', 30)->nullable()->default('#991b1b');
                $table->string('text_color', 30)->default('#ffffff');
                $table->string('border_color', 30)->nullable();
                $table->string('shape')->default('rounded')->comment('rounded, pill, square, slant, ribbon');
                $table->string('position')->default('top-left')->comment('top-left, top-right, bottom-left, bottom-right');
                $table->unsignedSmallInteger('size_scale')->default(100)->comment('Percentage scale relative to product card size (60 to 160)');
                $table->string('font_weight', 10)->default('800');
                $table->boolean('uppercase')->default(true);
                $table->boolean('pulse_animation')->default(false);
                $table->boolean('has_shadow')->default(true);
                $table->string('status', 20)->default('active');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_product_tag')) {
            Schema::create('product_product_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('product_tag_id')->constrained('product_tags')->cascadeOnDelete();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'product_tag_id']);
            });
        }

        // Seed default customizable tags if table is empty
        if (DB::table('product_tags')->count() === 0) {
            $now = now();
            DB::table('product_tags')->insert([
                [
                    'name' => 'NEW',
                    'slug' => 'new',
                    'icon' => 'fas fa-bolt',
                    'style_type' => 'gradient',
                    'bg_color' => '#10b981',
                    'bg_gradient' => '#059669',
                    'text_color' => '#ffffff',
                    'border_color' => null,
                    'shape' => 'rounded',
                    'position' => 'top-left',
                    'size_scale' => 100,
                    'font_weight' => '800',
                    'uppercase' => true,
                    'pulse_animation' => false,
                    'has_shadow' => true,
                    'status' => 'active',
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'TOP SELLER',
                    'slug' => 'top-seller',
                    'icon' => 'fas fa-fire',
                    'style_type' => 'gradient',
                    'bg_color' => '#f59e0b',
                    'bg_gradient' => '#ea580c',
                    'text_color' => '#ffffff',
                    'border_color' => null,
                    'shape' => 'pill',
                    'position' => 'top-left',
                    'size_scale' => 100,
                    'font_weight' => '800',
                    'uppercase' => true,
                    'pulse_animation' => true,
                    'has_shadow' => true,
                    'status' => 'active',
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'PROMO',
                    'slug' => 'promo',
                    'icon' => 'fas fa-percent',
                    'style_type' => 'solid',
                    'bg_color' => '#dc2626',
                    'bg_gradient' => '#991b1b',
                    'text_color' => '#ffffff',
                    'border_color' => null,
                    'shape' => 'rounded',
                    'position' => 'top-right',
                    'size_scale' => 105,
                    'font_weight' => '800',
                    'uppercase' => true,
                    'pulse_animation' => false,
                    'has_shadow' => true,
                    'status' => 'active',
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'EXCLUSIF',
                    'slug' => 'exclusif',
                    'icon' => 'fas fa-crown',
                    'style_type' => 'solid',
                    'bg_color' => '#111827',
                    'bg_gradient' => '#374151',
                    'text_color' => '#fbbf24',
                    'border_color' => '#fbbf24',
                    'shape' => 'rounded',
                    'position' => 'bottom-left',
                    'size_scale' => 95,
                    'font_weight' => '700',
                    'uppercase' => true,
                    'pulse_animation' => false,
                    'has_shadow' => true,
                    'status' => 'active',
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_tag');
        Schema::dropIfExists('product_tags');
    }
};

