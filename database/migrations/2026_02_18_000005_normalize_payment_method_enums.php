<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE store_sales SET payment_method = 'mobile_money' WHERE payment_method = 'mobile'");
            DB::statement("UPDATE store_sales SET payment_method = 'split' WHERE payment_method = 'mixed'");

            DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('cash','card','transfer','mobile_money','split') NOT NULL DEFAULT 'cash'");
            DB::statement("ALTER TABLE store_sales MODIFY payment_method ENUM('cash','card','transfer','mobile_money','split') NOT NULL DEFAULT 'cash'");
            DB::statement("ALTER TABLE store_payments MODIFY payment_method ENUM('cash','card','transfer','mobile_money') NOT NULL");
        }

        if ($driver === 'pgsql') {
            DB::statement("UPDATE store_sales SET payment_method = 'mobile_money' WHERE payment_method = 'mobile'");
            DB::statement("UPDATE store_sales SET payment_method = 'split' WHERE payment_method = 'mixed'");

            DB::statement("ALTER TABLE sales ALTER COLUMN payment_method TYPE VARCHAR(30)");
            DB::statement("ALTER TABLE sales ALTER COLUMN payment_method SET DEFAULT 'cash'");

            DB::statement("ALTER TABLE store_sales ALTER COLUMN payment_method TYPE VARCHAR(30)");
            DB::statement("ALTER TABLE store_sales ALTER COLUMN payment_method SET DEFAULT 'cash'");

            DB::statement("ALTER TABLE store_payments ALTER COLUMN payment_method TYPE VARCHAR(30)");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('cash','card','transfer','split') NOT NULL DEFAULT 'cash'");
            DB::statement("ALTER TABLE store_sales MODIFY payment_method ENUM('cash','card','transfer','mobile','mixed') NOT NULL");
            DB::statement("ALTER TABLE store_payments MODIFY payment_method ENUM('cash','card','transfer','mobile') NOT NULL");

            DB::statement("UPDATE store_sales SET payment_method = 'mobile' WHERE payment_method = 'mobile_money'");
            DB::statement("UPDATE store_sales SET payment_method = 'mixed' WHERE payment_method = 'split'");
        }
    }
};
