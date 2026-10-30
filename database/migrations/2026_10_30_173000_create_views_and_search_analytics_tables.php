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
        // 1. Add views_count to products table if missing
        if (!Schema::hasColumn('products', 'views_count')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('views_count')->default(0)->after('status')->index();
            });
        }

        // 2. Add views_count to categories table if missing
        if (!Schema::hasColumn('categories', 'views_count')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('views_count')->default(0)->after('status')->index();
            });
        }

        // 3. Detailed Product Views with Date
        if (!Schema::hasTable('product_views')) {
            Schema::create('product_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('ip_address', 45)->nullable();
                $table->string('session_id', 100)->nullable();
                $table->string('device_type', 20)->default('desktop'); // desktop, mobile, tablet
                $table->string('referer', 255)->nullable();
                $table->date('viewed_date')->index();
                $table->timestamps();

                $table->index(['product_id', 'viewed_date']);
                $table->index(['viewed_date', 'device_type']);
            });
        }

        // 4. Detailed Category Views with Date
        if (!Schema::hasTable('category_views')) {
            Schema::create('category_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('ip_address', 45)->nullable();
                $table->string('session_id', 100)->nullable();
                $table->string('device_type', 20)->default('desktop');
                $table->date('viewed_date')->index();
                $table->timestamps();

                $table->index(['category_id', 'viewed_date']);
            });
        }

        // 5. Global Search Words & Logs with Count, Results Count & Date
        if (!Schema::hasTable('search_logs')) {
            Schema::create('search_logs', function (Blueprint $table) {
                $table->id();
                $table->string('query', 191);
                $table->string('query_normalized', 191)->index();
                $table->unsignedInteger('results_count')->default(0)->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('ip_address', 45)->nullable();
                $table->string('session_id', 100)->nullable();
                $table->string('device_type', 20)->default('desktop');
                $table->date('searched_date')->index();
                $table->timestamps();

                $table->index(['query_normalized', 'searched_date']);
                $table->index(['results_count', 'searched_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('category_views');
        Schema::dropIfExists('product_views');

        if (Schema::hasColumn('products', 'views_count')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }

        if (Schema::hasColumn('categories', 'views_count')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }
    }
};

