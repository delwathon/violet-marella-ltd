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
                'legal_name' => 'Violet Marella Lounge',
                'slug' => 'lounge',
                'description' => 'Mini lounge sales and operations.',
                'phone' => '+234 800 000 0000',
                'email' => 'lounge@violetmarella.com',
                'address' => '123 Business Street, Ibadan, Oyo State',
                'rc_number' => '',
                'website' => '',
                'tax_id' => '',
                'contact_person' => '',
                'icon' => 'store',
                'color' => 'success',
                'is_active' => true,
            ],
            [
                'name' => 'Anire Craft Store',
                'legal_name' => 'Anire Craft Store',
                'slug' => 'gift_store',
                'description' => 'Anire craft store sales and inventory.',
                'phone' => '+234 814 648 2898',
                'email' => 'anire@violetmarella.com',
                'address' => '123 Business Street, Ibadan, Oyo State',
                'rc_number' => '',
                'website' => '',
                'tax_id' => '',
                'contact_person' => '',
                'icon' => 'gift',
                'color' => 'danger',
                'is_active' => true,
            ],
            [
                'name' => 'Photo Studio',
                'legal_name' => 'Violet Marella Photo Studio',
                'slug' => 'photo_studio',
                'description' => 'Photo studio sessions, customers, and reports.',
                'phone' => '+234 800 000 0000',
                'email' => 'studio@violetmarella.com',
                'address' => '123 Business Street, Ibadan, Oyo State',
                'rc_number' => '',
                'website' => '',
                'tax_id' => '',
                'contact_person' => '',
                'icon' => 'camera',
                'color' => 'primary',
                'is_active' => true,
            ],
            [
                'name' => 'Prop Rental',
                'legal_name' => 'Violet Marella Prop Rental',
                'slug' => 'prop_rental',
                'description' => 'Prop rental inventory and bookings.',
                'phone' => '+234 800 000 0000',
                'email' => 'props@violetmarella.com',
                'address' => '123 Business Street, Ibadan, Oyo State',
                'rc_number' => '',
                'website' => '',
                'tax_id' => '',
                'contact_person' => '',
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
