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
     * Studio Settings - Global configuration for the Photo Studio module
     */
    public function up(): void
    {
        Schema::create('studio_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
        });

        // Insert default settings
        DB::table('studio_settings')->insert([
            [
                'key' => 'offset_time',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Preparation time in minutes before session timer starts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_base_time',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Default base time in minutes for new categories',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_base_price',
                'value' => '30000',
                'type' => 'integer',
                'description' => 'Default base price in Naira for new categories',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'allow_overtime',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow sessions to continue past booked duration with overtime charges',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => '₦',
                'type' => 'string',
                'description' => 'Currency symbol for display',
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
        Schema::dropIfExists('studio_settings');
    }
};