<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = [
            [
                'employee_id' => 'EMP001',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@violetmarella.com',
                'phone' => '+234 800 000 0000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'hire_date' => now()->subYear(),
                'address' => '123 Admin Street, Lagos',
                'emergency_contact' => 'Admin Emergency',
                'emergency_phone' => '+234 800 000 0001',
                'permissions' => ['all'],
            ],
            [
                'employee_id' => 'EMP002',
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager@violetmarella.com',
                'phone' => '+234 800 000 0002',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'hire_date' => now()->subMonths(6),
                'address' => '456 Manager Avenue, Lagos',
                'emergency_contact' => 'Manager Emergency',
                'emergency_phone' => '+234 800 000 0003',
                'permissions' => ['sales', 'inventory', 'reports', 'customers'],
            ],
            [
                'employee_id' => 'EMP003',
                'first_name' => 'John',
                'last_name' => 'Cashier',
                'email' => 'cashier@violetmarella.com',
                'phone' => '+234 800 000 0004',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'hire_date' => now()->subMonths(3),
                'address' => '789 Cashier Road, Lagos',
                'emergency_contact' => 'Cashier Emergency',
                'emergency_phone' => '+234 800 000 0005',
                'permissions' => ['sales', 'customers'],
            ],
            [
                'employee_id' => 'EMP004',
                'first_name' => 'Jane',
                'last_name' => 'Stock Keeper',
                'email' => 'stock@violetmarella.com',
                'phone' => '+234 800 000 0006',
                'password' => Hash::make('password'),
                'role' => 'stock_keeper',
                'hire_date' => now()->subMonths(2),
                'address' => '321 Stock Street, Lagos',
                'emergency_contact' => 'Stock Emergency',
                'emergency_phone' => '+234 800 000 0007',
                'permissions' => ['inventory'],
            ],
        ];

        foreach ($staff as $staffMember) {
            Staff::create($staffMember);
        }
    }
}
