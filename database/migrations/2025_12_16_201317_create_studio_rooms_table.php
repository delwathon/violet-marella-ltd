<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Studio Rooms - Optional tracking of physical rooms within each category
     * Admin can choose to track or not track individual rooms
     */
    public function up(): void
    {
        Schema::create('studio_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('studio_categories')
                  ->onDelete('cascade');
            
            $table->string('name');                                          // e.g., "Classic Room A", "Deluxe Suite 1"
            $table->string('code')->unique();                                // e.g., "CL-A", "DX-1"
            $table->text('description')->nullable();
            
            // Room Details
            $table->integer('floor')->nullable();                            // Floor number
            $table->string('location')->nullable();                          // Location description
            $table->integer('size_sqm')->nullable();                         // Room size in square meters
            
            // Equipment & Features
            $table->json('equipment')->nullable();                           // List of equipment in the room
            $table->json('features')->nullable();                            // Special features
            
            // Status
            $table->enum('status', ['available', 'maintenance', 'out_of_service'])
                  ->default('available');
            $table->text('maintenance_notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_rooms');
    }
};