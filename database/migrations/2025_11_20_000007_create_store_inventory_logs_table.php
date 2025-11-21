<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_product_id')->constrained('store_products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['sale', 'purchase', 'adjustment', 'return', 'damage', 'expiry', 'transfer']);
            $table->integer('quantity_change');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamp('action_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_inventory_logs');
    }
};