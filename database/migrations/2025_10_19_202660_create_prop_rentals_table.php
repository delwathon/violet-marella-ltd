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
        Schema::create('prop_rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_id')->unique(); // e.g., rental-001
            $table->foreignId('prop_id')->constrained('props')->onDelete('cascade');
            $table->foreignId('rental_customer_id')->constrained('rental_customers')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'overdue', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->boolean('agreement_signed')->default(false);
            $table->dateTime('returned_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prop_rentals');
    }
};