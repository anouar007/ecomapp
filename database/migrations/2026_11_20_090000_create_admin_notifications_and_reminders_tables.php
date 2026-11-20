<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_notification_states')) {
            Schema::create('admin_notification_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('alert_key', 140);
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamp('snoozed_until')->nullable()->index();
                $table->timestamp('dismissed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'alert_key']);
            });
        }

        if (!Schema::hasTable('admin_reminders')) {
            Schema::create('admin_reminders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title', 180);
                $table->text('notes')->nullable();
                $table->string('category', 32)->default('general');
                $table->string('priority', 16)->default('high');
                $table->timestamp('due_at')->nullable()->index();
                $table->string('related_url', 255)->nullable();
                $table->boolean('is_completed')->default(false)->index();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_reminders');
        Schema::dropIfExists('admin_notification_states');
    }
};
