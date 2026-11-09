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
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 50)->index();
            $table->decimal('amount', 12, 2);
            $table->string('frequency', 20)->default('monthly'); // monthly, quarterly, yearly, weekly
            $table->unsignedTinyInteger('day_of_month')->default(1);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('payment_method', 30)->default('bank_transfer');
            $table->string('payee')->nullable();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('auto_generate')->default(true);
            $table->date('last_generated_at')->nullable();
            $table->date('next_due_date')->nullable()->index();
            $table->timestamps();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('recurring_expense_id')->nullable()->after('store_id')->constrained('recurring_expenses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['recurring_expense_id']);
            $table->dropColumn('recurring_expense_id');
        });

        Schema::dropIfExists('recurring_expenses');
    }
};
