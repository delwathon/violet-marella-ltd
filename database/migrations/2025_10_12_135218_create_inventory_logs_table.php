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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('action_type', ['sale', 'purchase', 'adjustment', 'return', 'damage', 'expiry', 'transfer']);
            $table->integer('quantity_change'); // Positive for additions, negative for deductions
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('reference_number')->nullable(); // Sale receipt, purchase order, etc.
            $table->timestamp('action_date');
            $table->timestamps();
            
            $table->index(['product_id', 'action_date']);
            $table->index(['action_type', 'action_date']);
            $table->index(['staff_id', 'action_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};