<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = [
            [
                'name' => 'Lounge',
                'slug' => 'lounge',
                'description' => 'Mini lounge sales and operations.',
                'icon' => 'store',
                'color' => 'success',
                'is_active' => true,
            ],
            [
                'name' => 'Anire Craft Store',
                'slug' => 'gift_store',
                'description' => 'Anire craft store sales and inventory.',
                'icon' => 'gift',
                'color' => 'danger',
                'is_active' => true,
            ],
            [
                'name' => 'Photo Studio',
                'slug' => 'photo_studio',
                'description' => 'Photo studio sessions, customers, and reports.',
                'icon' => 'camera',
                'color' => 'primary',
                'is_active' => true,
            ],
            [
                'name' => 'Prop Rental',
                'slug' => 'prop_rental',
                'description' => 'Prop rental inventory and bookings.',
                'icon' => 'guitar',
                'color' => 'warning',
                'is_active' => true,
            ],
        ];

        foreach ($businesses as $business) {
            Business::updateOrCreate(['slug' => $business['slug']], $business);
        }

        $allBusinessIds = Business::query()->pluck('id')->all();

        if ($allBusinessIds !== []) {
            User::query()
                ->whereIn('role', ['superadmin', 'admin'])
                ->get()
                ->each(function (User $user) use ($allBusinessIds): void {
                    $user->businesses()->syncWithoutDetaching($allBusinessIds);
                });
        }
    }
}
