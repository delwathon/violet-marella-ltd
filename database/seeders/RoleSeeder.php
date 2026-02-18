<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full administrative access to all modules.',
                'color' => 'danger',
                'permissions' => ['*'],
                'is_system' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manages operations and reports across business units.',
                'color' => 'primary',
                'permissions' => [
                    'dashboard.view',
                    'sales.manage',
                    'inventory.manage',
                    'customers.manage',
                    'reports.view',
                    'reports.export',
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'description' => 'Handles checkout and customer transactions.',
                'color' => 'success',
                'permissions' => [
                    'dashboard.view',
                    'sales.create',
                    'sales.view',
                    'customers.view',
                    'customers.create',
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Stock Keeper',
                'slug' => 'stock_keeper',
                'description' => 'Maintains stock and inventory records.',
                'color' => 'info',
                'permissions' => [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.manage',
                    'products.view',
                    'products.edit',
                ],
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
