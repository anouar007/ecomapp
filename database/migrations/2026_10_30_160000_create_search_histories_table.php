<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores the search history of logged-in clients so they can
     * see and reuse their previous searches from any device.
     */
    public function up(): void
    {
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('query', 150);
            $table->string('query_key', 150); // normalized (lowercase/trimmed) for de-duplication
            $table->unsignedInteger('search_count')->default(1);
            $table->timestamp('last_searched_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'query_key']);
            $table->index(['user_id', 'last_searched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};

