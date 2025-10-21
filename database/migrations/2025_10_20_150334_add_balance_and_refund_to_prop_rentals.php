<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add balance_due, cancelled_at, cancelled_by, and refund_amount fields
 * to prop_rentals table
 * 
 * Run: php artisan make:migration add_balance_and_refund_to_prop_rentals
 * Then replace with this code
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prop_rentals', function (Blueprint $table) {
            // Balance tracking
            $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
            
            // Cancellation tracking
            $table->timestamp('cancelled_at')->nullable()->after('returned_at');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
            $table->decimal('refund_amount', 10, 2)->default(0)->after('cancelled_by');
            
            // Add foreign key for cancelled_by
            $table->foreign('cancelled_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prop_rentals', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'balance_due',
                'cancelled_at',
                'cancelled_by',
                'refund_amount'
            ]);
        });
    }
};