<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->default('primary');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('business_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'user_id']);
            $table->index('user_id');
        });

        $now = now();
        $businessRows = [
            [
                'name' => 'Lounge',
                'slug' => 'lounge',
                'description' => 'Mini lounge sales and operations.',
                'icon' => 'store',
                'color' => 'success',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Anire Craft Store',
                'slug' => 'gift_store',
                'description' => 'Anire craft store sales and inventory.',
                'icon' => 'gift',
                'color' => 'danger',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Photo Studio',
                'slug' => 'photo_studio',
                'description' => 'Photo studio sessions, customers, and reports.',
                'icon' => 'camera',
                'color' => 'primary',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Prop Rental',
                'slug' => 'prop_rental',
                'description' => 'Prop rental inventory and bookings.',
                'icon' => 'guitar',
                'color' => 'warning',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('businesses')->insert($businessRows);

        $businessIds = DB::table('businesses')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();
        $pivotRows = [];

        foreach ($userIds as $userId) {
            foreach ($businessIds as $businessId) {
                $pivotRows[] = [
                    'business_id' => $businessId,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($pivotRows !== []) {
            DB::table('business_user')->insert($pivotRows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business_user');
        Schema::dropIfExists('businesses');
    }
};
