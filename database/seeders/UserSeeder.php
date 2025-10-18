<?php

namespace Database\Seeders;

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
        $user = [
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
                'permissions' => ['all'],
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
                'permissions' => ['sales', 'inventory', 'reports', 'customers'],
            ],
            [
                'first_name' => 'Cashier',
                'last_name' => 'User',
                'email' => 'cashier@violetmarella.com',
                'phone' => '+234 800 000 0004',
                'password' => Hash::make('cashier123'),
                'role' => 'cashier',
                'hire_date' => now()->subMonths(3),
                'address' => '789 Cashier Road, Lagos',
                'emergency_contact' => 'Cashier Emergency',
                'emergency_phone' => '+234 800 000 0005',
                'permissions' => ['sales', 'customers'],
            ],
            [
                'first_name' => 'User',
                'last_name' => 'User',
                'email' => 'staff@violetmarella.com',
                'phone' => '+234 800 000 0006',
                'password' => Hash::make('staff123'),
                'role' => 'stock_keeper',
                'hire_date' => now()->subMonths(2),
                'address' => '321 Stock Street, Lagos',
                'emergency_contact' => 'Stock Emergency',
                'emergency_phone' => '+234 800 000 0007',
                'permissions' => ['inventory'],
            ],
        ];

        foreach ($user as $users) {
            User::create($users);
        }
    }
}
