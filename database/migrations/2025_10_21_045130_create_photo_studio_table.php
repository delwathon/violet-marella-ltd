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
        // Studio Rates Table - CREATE THIS FIRST
        Schema::create('studio_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('base_time')->comment('in minutes');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('per_minute_rate', 10, 2);
            $table->decimal('hourly_rate', 10, 2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Studios Table
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->foreignId('studio_rate_id')->nullable()->constrained('studio_rates')->onDelete('set null');
            $table->integer('capacity')->default(1);
            $table->json('equipment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Studio Customers Table
        Schema::create('studio_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('total_sessions')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamp('last_visit')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Studio Sessions Table
        Schema::create('studio_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('studio_customers')->onDelete('cascade');
            $table->foreignId('studio_rate_id')->nullable()->constrained('studio_rates')->onDelete('set null');
            $table->string('session_code')->unique();
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->integer('expected_duration')->comment('in minutes');
            $table->integer('actual_duration')->nullable()->comment('in minutes');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('extra_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });

        // Studio Payments Table
        Schema::create('studio_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('studio_sessions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other']);
            $table->string('reference')->nullable();
            $table->timestamp('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_payments');
        Schema::dropIfExists('studio_sessions');
        Schema::dropIfExists('studios');
        Schema::dropIfExists('studio_customers');
        Schema::dropIfExists('studio_rates');
    }
};