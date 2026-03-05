<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $expand = static function (array $modules): array {
            $permissions = [];

            foreach ($modules as $module) {
                $permissions[] = "{$module}.view";
                $permissions[] = "{$module}.manage";
            }

            return $permissions;
        };

        $allBusinessAccess = [
            'lounge.access',
            'gift_store.access',
            'photo_studio.access',
            'prop_rental.access',
        ];

        $allBusinessModules = [
            'lounge.pos',
            'lounge.products',
            'lounge.categories',
            'lounge.customers',
            'lounge.sales',
            'lounge.inventory',
            'gift_store.pos',
            'gift_store.products',
            'gift_store.categories',
            'gift_store.customers',
            'gift_store.sales',
            'gift_store.inventory',
            'photo_studio.dashboard',
            'photo_studio.settings',
            'photo_studio.categories',
            'photo_studio.rooms',
            'photo_studio.customers',
            'photo_studio.sessions',
            'photo_studio.reports',
            'prop_rental.dashboard',
            'prop_rental.props',
            'prop_rental.rentals',
            'prop_rental.customers',
            'prop_rental.calendar',
            'prop_rental.reports',
        ];

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'superadmin',
                'description' => 'Full access to every business and system capability.',
                'color' => 'danger',
                'permissions' => ['*', 'system.update'],
                'is_system' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Legacy administrator role with full access.',
                'color' => 'danger',
                'permissions' => ['*', 'system.update'],
                'is_system' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manages assigned business units with broad module access.',
                'color' => 'primary',
                'permissions' => array_values(array_unique(array_merge(
                    [
                        'dashboard.view',
                        'reports.view',
                        'reports.export',
                    ],
                    $allBusinessAccess,
                    $expand($allBusinessModules)
                ))),
                'is_system' => true,
            ],
            [
                'name' => 'Sales Representative',
                'slug' => 'sales_representative',
                'description' => 'Handles sales and customer workflows in assigned sales businesses.',
                'color' => 'success',
                'permissions' => array_values(array_unique(array_merge(
                    [
                        'dashboard.view',
                        'reports.view',
                        'lounge.access',
                        'gift_store.access',
                    ],
                    $expand([
                        'lounge.pos',
                        'lounge.sales',
                        'lounge.customers',
                        'gift_store.pos',
                        'gift_store.sales',
                        'gift_store.customers',
                    ]),
                    [
                        'lounge.products.view',
                        'gift_store.products.view',
                    ]
                ))),
                'is_system' => true,
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Manages front-desk operations for studio and prop rental modules.',
                'color' => 'info',
                'permissions' => array_values(array_unique(array_merge(
                    [
                        'dashboard.view',
                        'reports.view',
                        'photo_studio.access',
                        'prop_rental.access',
                    ],
                    $expand([
                        'photo_studio.dashboard',
                        'photo_studio.customers',
                        'photo_studio.sessions',
                        'prop_rental.dashboard',
                        'prop_rental.customers',
                        'prop_rental.rentals',
                    ])
                ))),
                'is_system' => true,
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'description' => 'Handles point-of-sale and customer transactions.',
                'color' => 'success',
                'permissions' => array_values(array_unique(array_merge(
                    [
                        'dashboard.view',
                        'lounge.access',
                        'gift_store.access',
                    ],
                    $expand([
                        'lounge.pos',
                        'lounge.sales',
                        'lounge.customers',
                        'gift_store.pos',
                        'gift_store.sales',
                        'gift_store.customers',
                    ]),
                    [
                        'lounge.products.view',
                        'gift_store.products.view',
                    ]
                ))),
                'is_system' => true,
            ],
            [
                'name' => 'Stock Keeper',
                'slug' => 'stock_keeper',
                'description' => 'Maintains inventory, categories, and product catalog.',
                'color' => 'info',
                'permissions' => array_values(array_unique(array_merge(
                    [
                        'dashboard.view',
                        'reports.view',
                        'lounge.access',
                        'gift_store.access',
                    ],
                    $expand([
                        'lounge.products',
                        'lounge.categories',
                        'lounge.inventory',
                        'gift_store.products',
                        'gift_store.categories',
                        'gift_store.inventory',
                    ])
                ))),
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                Arr::only($role, ['name', 'slug', 'description', 'color', 'permissions', 'is_system'])
            );
        }
    }
}
