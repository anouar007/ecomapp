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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('transaction_number', 50)->unique();
            $table->enum('type', [
                'payment_received',  // Client pays store (reduces debt or creates credit balance)
                'refund_to_client',  // Store pays back client (refunds credit balance/deposit)
                'credit_given',      // Credit sale / delivery on credit (increases debt)
                'credit_note',       // Return / discount given as store credit (store owes client)
                'adjustment'         // Manual ledger correction
            ])->default('payment_received');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->string('payment_method', 50)->default('especes'); // especes, virement, cheque, tpe, autre
            $table->string('reference_number', 100)->nullable();     // N° Chèque, ref virement
            $table->string('proof_image', 255)->nullable();          // Photo du reçu / bordereau / chèque
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->dateTime('payment_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'payment_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
