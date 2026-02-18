<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Studio Customers - Updated customer table
     * Note: If old studio_customers table exists, this creates a new version
     */
    public function up(): void
    {
        // Drop old table if exists (fresh start approach)
        Schema::dropIfExists('studio_payments');
        Schema::dropIfExists('studio_sessions');
        Schema::dropIfExists('studio_customers');
        
        Schema::create('studio_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Statistics (auto-updated)
            $table->integer('total_sessions')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamp('last_visit')->nullable();
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->json('preferences')->nullable();                         // Customer preferences
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['phone']);
            $table->index(['email']);
            $table->index(['is_active', 'is_blacklisted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_customers');
    }
};