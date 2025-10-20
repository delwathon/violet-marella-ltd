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
        Schema::create('rental_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique(); // e.g., cust-001
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('id_number')->nullable(); // National ID, Driver's License, etc.
            $table->integer('total_rentals')->default(0);
            $table->integer('current_rentals')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_customers');
    }
};