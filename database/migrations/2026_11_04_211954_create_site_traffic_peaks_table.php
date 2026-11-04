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
        Schema::create('site_traffic_peaks', function (Blueprint $table) {
            $table->id();
            $table->date('record_date')->unique();
            $table->unsignedInteger('year')->index();
            $table->unsignedTinyInteger('month')->index();     // 1-12
            $table->unsignedTinyInteger('week')->index();      // 1-53 (ISO-8601)
            $table->unsignedInteger('peak_concurrent_users')->default(0); // Max online users recorded simultaneously
            $table->time('peak_time')->nullable();                         // Time of day when concurrent peak occurred
            $table->unsignedInteger('unique_visitors')->default(0);        // Total distinct IP visitors on that date
            $table->unsignedInteger('total_views')->default(0);            // Total views/hits on that date
            $table->timestamps();

            $table->index(['year', 'month']);
            $table->index(['year', 'week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_traffic_peaks');
    }
};
