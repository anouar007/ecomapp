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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('title');
            $table->string('subtitle')->nullable()->after('badge');
            $table->text('description')->nullable()->after('subtitle');
            $table->string('button_text')->nullable()->after('link');
            $table->text('features')->nullable()->after('button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['badge', 'subtitle', 'description', 'button_text', 'features']);
        });
    }
};
