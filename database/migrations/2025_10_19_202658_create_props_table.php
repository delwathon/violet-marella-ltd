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
        Schema::create('props', function (Blueprint $table) {
            $table->id();
            $table->string('prop_id')->unique(); // e.g., inst-001
            $table->string('name');
            $table->string('category'); // guitars, keyboards, drums, brass, strings
            $table->string('type');
            $table->string('brand');
            $table->string('model');
            $table->decimal('daily_rate', 10, 2);
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // FontAwesome icon class
            $table->string('serial_number')->unique();
            $table->date('purchase_date')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('props');
    }
};