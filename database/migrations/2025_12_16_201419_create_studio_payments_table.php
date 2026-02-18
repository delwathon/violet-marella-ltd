<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Studio Payments - Payment records for sessions
     * Supports multiple payments per session (for partial payments)
     */
    public function up(): void
    {
        Schema::create('studio_payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('session_id')
                  ->constrained('studio_sessions')
                  ->onDelete('cascade');
            
            // Payment Details
            $table->string('reference')->unique();                           // Payment reference/receipt number
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other']);
            $table->enum('payment_type', ['booking', 'overtime', 'full', 'partial', 'refund'])
                  ->default('full');
            
            // Transaction Info
            $table->string('transaction_reference')->nullable();             // External transaction ref (POS, bank)
            $table->timestamp('payment_date');
            
            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                  ->default('completed');
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');                                    // Staff who received payment
            
            $table->timestamps();
            
            $table->index(['session_id', 'status']);
            $table->index(['payment_date']);
            $table->index(['payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_payments');
    }
};