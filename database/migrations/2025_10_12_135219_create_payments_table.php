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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'mobile_money']);
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable(); // Transaction reference
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->json('payment_details')->nullable(); // Store card last 4 digits, bank info, etc.
            $table->text('notes')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
            
            $table->index(['sale_id', 'payment_method']);
            $table->index(['payment_method', 'payment_date']);
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};