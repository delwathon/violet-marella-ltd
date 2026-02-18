<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Studio Categories - Room categories like Classic, Deluxe, etc.
     * This replaces the old studio_rates concept with a more comprehensive category system
     */
    public function up(): void
    {
        Schema::create('studio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                          // e.g., "Classic", "Deluxe"
            $table->string('slug')->unique();                                // e.g., "classic", "deluxe"
            $table->text('description')->nullable();                         // Category description
            $table->string('color')->default('#6366f1');                     // UI color for identification
            
            // Pricing Configuration
            $table->integer('base_time')->default(30);                       // Base time in minutes
            $table->decimal('base_price', 12, 2);                            // Base price for base_time
            $table->decimal('per_minute_rate', 10, 2)->nullable();           // Auto-calculated: base_price / base_time
            $table->decimal('hourly_rate', 12, 2)->nullable();               // Auto-calculated: per_minute_rate * 60
            
            // Capacity & Concurrency
            $table->integer('max_occupants')->default(4);                    // Max people allowed per session
            $table->integer('max_concurrent_sessions')->default(3);          // How many sessions can run simultaneously
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);                       // For display ordering
            
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Insert default categories
        DB::table('studio_categories')->insert([
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Standard studio rooms perfect for individual and small group photo sessions',
                'color' => '#3b82f6',
                'base_time' => 30,
                'base_price' => 30000.00,
                'per_minute_rate' => 1000.00,      // 30000 / 30
                'hourly_rate' => 60000.00,         // 1000 * 60
                'max_occupants' => 4,
                'max_concurrent_sessions' => 3,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deluxe',
                'slug' => 'deluxe',
                'description' => 'Premium studio rooms with advanced equipment and larger space for professional shoots',
                'color' => '#8b5cf6',
                'base_time' => 30,
                'base_price' => 50000.00,
                'per_minute_rate' => 1666.67,      // 50000 / 30
                'hourly_rate' => 100000.00,        // 1666.67 * 60
                'max_occupants' => 6,
                'max_concurrent_sessions' => 2,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_categories');
    }
};