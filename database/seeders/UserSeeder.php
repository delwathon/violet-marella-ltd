<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allBusinessSlugs = Business::query()->pluck('slug')->all();
        $businessIdsBySlug = Business::query()->pluck('id', 'slug');

        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@violetmarella.com',
                'phone' => '+234 800 000 0009',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'hire_date' => now()->subYears(2),
                'address' => '1 Violet Marella HQ, Lagos',
                'emergency_contact' => 'Executive Assistant',
                'emergency_phone' => '+234 800 000 0010',
                'permissions' => ['*'],
                'business_slugs' => $allBusinessSlugs,
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@violetmarella.com',
                'phone' => '+234 800 000 0000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'hire_date' => now()->subYear(),
                'address' => '123 Admin Street, Lagos',
                'emergency_contact' => 'Admin Emergency',
                'emergency_phone' => '+234 800 000 0001',
                'permissions' => ['*'],
                'business_slugs' => $allBusinessSlugs,
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager@violetmarella.com',
                'phone' => '+234 800 000 0002',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'hire_date' => now()->subMonths(6),
                'address' => '456 Manager Avenue, Lagos',
                'emergency_contact' => 'Manager Emergency',
                'emergency_phone' => '+234 800 000 0003',
                'permissions' => [],
                'business_slugs' => ['lounge', 'gift_store'],
            ],
            [
                'first_name' => 'Sales',
                'last_name' => 'Rep',
                'email' => 'salesrep@violetmarella.com',
                'phone' => '+234 800 000 0004',
                'password' => Hash::make('salesrep123'),
                'role' => 'sales_representative',
                'hire_date' => now()->subMonths(3),
                'address' => '789 Cashier Road, Lagos',
                'emergency_contact' => 'Sales Emergency',
                'emergency_phone' => '+234 800 000 0005',
                'permissions' => [],
                'business_slugs' => ['lounge', 'gift_store'],
            ],
            [
                'first_name' => 'Studio',
                'last_name' => 'Reception',
                'email' => 'reception@violetmarella.com',
                'phone' => '+234 800 000 0006',
                'password' => Hash::make('reception123'),
                'role' => 'receptionist',
                'hire_date' => now()->subMonths(2),
                'address' => '321 Stock Street, Lagos',
                'emergency_contact' => 'Reception Emergency',
                'emergency_phone' => '+234 800 000 0007',
                'permissions' => [],
                'business_slugs' => ['photo_studio', 'prop_rental'],
            ],
        ];

        foreach ($users as $record) {
            $businessSlugs = $record['business_slugs'] ?? [];
            unset($record['business_slugs']);

            $user = User::updateOrCreate(
                ['email' => $record['email']],
                $record
            );

            if ($user->role === 'admin' || $user->role === 'superadmin') {
                $user->businesses()->sync($businessIdsBySlug->values()->all());
                continue;
            }

            $businessIds = collect($businessSlugs)
                ->map(fn ($slug) => $businessIdsBySlug[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            $user->businesses()->sync($businessIds);
        }
    }
}
