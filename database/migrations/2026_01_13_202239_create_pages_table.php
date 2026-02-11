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
        Schema::create('pages', function (Blueprint $row) {
            $row->id();
            $row->string('title');
            $row->string('slug')->unique();
            $row->longText('content')->nullable(); // Store JSON blocks
            $row->string('layout')->default('default');
            $row->json('settings')->nullable();
            $row->string('meta_title')->nullable();
            $row->text('meta_description')->nullable();
            $row->boolean('is_published')->default(false);
            $row->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
