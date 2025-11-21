<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_sale_id')->constrained('store_sales')->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'mobile']);
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->timestamp('payment_date');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_payments');
    }
};