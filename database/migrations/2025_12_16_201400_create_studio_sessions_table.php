<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Studio Sessions - Completely redesigned for category-based booking
     * Key Changes:
     * - Links to category instead of individual studio
     * - Tracks number of people in party
     * - Separates scheduled_start from actual_start (offset time)
     */
    public function up(): void
    {
        Schema::create('studio_sessions', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('category_id')
                  ->constrained('studio_categories')
                  ->onDelete('restrict');                                    // Prevent deleting category with sessions
            $table->foreignId('customer_id')
                  ->constrained('studio_customers')
                  ->onDelete('restrict');                                    // Prevent deleting customer with sessions
            
            // Session Identification
            $table->string('session_code')->unique();                        // e.g., "SS-ABC123"
            $table->string('qr_code')->nullable()->unique();                 // For QR-based operations
            
            // Party Details
            $table->integer('number_of_people')->default(1);                 // How many people in this session
            $table->json('party_names')->nullable();                         // Optional: names of people in party
            
            // Time Management
            $table->timestamp('check_in_time');                              // When customer checked in
            $table->timestamp('scheduled_start_time');                       // check_in_time + offset_time
            $table->timestamp('actual_start_time')->nullable();              // When timer actually started
            $table->timestamp('check_out_time')->nullable();                 // When customer checked out
            
            // Duration
            $table->integer('booked_duration');                              // Duration customer booked (minutes)
            $table->integer('offset_time_applied')->default(0);              // Offset time that was applied (minutes)
            $table->integer('actual_duration')->nullable();                  // Actual time used (minutes)
            $table->integer('overtime_duration')->nullable();                // Minutes over booked time
            
            // Pricing (snapshot at time of booking)
            $table->decimal('rate_base_time', 10, 2);                        // Category's base_time at booking
            $table->decimal('rate_base_price', 12, 2);                       // Category's base_price at booking
            $table->decimal('rate_per_minute', 10, 2);                       // Category's per_minute_rate at booking
            
            // Calculated Amounts
            $table->decimal('base_amount', 12, 2);                           // Base charge for booked time
            $table->decimal('overtime_amount', 12, 2)->default(0);           // Extra charges for overtime
            $table->decimal('discount_amount', 12, 2)->default(0);           // Any discounts applied
            $table->decimal('total_amount', 12, 2);                          // Final amount
            
            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])
                  ->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])
                  ->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);                   // For partial payments
            
            // Session Status
            $table->enum('status', [
                'pending',      // Checked in, waiting for prep time
                'active',       // Session timer running
                'overtime',     // Past booked duration, still active
                'completed',    // Session ended normally
                'cancelled',    // Session cancelled
                'no_show'       // Customer didn't show up
            ])->default('pending');
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');                                    // Staff who created the session
            $table->foreignId('checked_out_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');                                    // Staff who checked out
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['status', 'category_id']);
            $table->index(['customer_id', 'status']);
            $table->index(['check_in_time']);
            $table->index(['scheduled_start_time']);
            $table->index(['payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_sessions');
    }
};