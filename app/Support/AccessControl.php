<?php

namespace App\Support;

class AccessControl
{
    /**
     * Canonical business catalog.
     *
     * @return array<string, array<string, string>>
     */
    public static function businesses(): array
    {
        return [
            'lounge' => [
                'name' => 'Lounge',
                'route_prefix' => 'lounge',
                'route_name' => 'lounge',
            ],
            'gift_store' => [
                'name' => 'Anire Craft Store',
                'route_prefix' => 'anire-craft-store',
                'route_name' => 'anire-craft-store',
            ],
            'photo_studio' => [
                'name' => 'Photo Studio',
                'route_prefix' => 'photo-studio',
                'route_name' => 'photo-studio',
            ],
            'prop_rental' => [
                'name' => 'Prop Rental',
                'route_prefix' => 'prop-rental',
                'route_name' => 'prop-rental',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function businessSlugs(): array
    {
        return array_keys(self::businesses());
    }

    public static function resolveBusinessSlug(string $value): ?string
    {
        $value = trim(strtolower($value));

        if ($value === '') {
            return null;
        }

        $aliases = [
            'anire-craft-store' => 'gift_store',
            'gift-store' => 'gift_store',
            'gift_store' => 'gift_store',
            'photo-studio' => 'photo_studio',
            'photo_studio' => 'photo_studio',
            'prop-rental' => 'prop_rental',
            'prop_rental' => 'prop_rental',
            'lounge' => 'lounge',
        ];

        $resolved = $aliases[$value] ?? $value;

        return in_array($resolved, self::businessSlugs(), true) ? $resolved : null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function permissionGroups(): array
    {
        $groups = [
            'Core' => [
                'dashboard.view',
                'reports.view',
                'reports.export',
                'settings.manage',
                'system.update',
                'users.view',
                'users.manage',
                'roles.view',
                'roles.manage',
                'departments.view',
                'departments.manage',
                'security.manage',
            ],
            'Business Access' => [
                'lounge.access',
                'gift_store.access',
                'photo_studio.access',
                'prop_rental.access',
            ],
        ];

        $groups['Lounge'] = self::buildModulePermissions('lounge', [
            'pos',
            'products',
            'categories',
            'customers',
            'sales',
            'inventory',
        ]);

        $groups['Anire Craft Store'] = self::buildModulePermissions('gift_store', [
            'pos',
            'products',
            'categories',
            'customers',
            'sales',
            'inventory',
        ]);

        $groups['Photo Studio'] = self::buildModulePermissions('photo_studio', [
            'dashboard',
            'settings',
            'categories',
            'rooms',
            'customers',
            'sessions',
            'reports',
        ]);

        $groups['Prop Rental'] = self::buildModulePermissions('prop_rental', [
            'dashboard',
            'props',
            'rentals',
            'customers',
            'calendar',
            'reports',
        ]);

        return $groups;
    }

    /**
     * @return array<int, string>
     */
    public static function allPermissions(): array
    {
        $permissions = [];

        foreach (self::permissionGroups() as $groupPermissions) {
            foreach ($groupPermissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * @param array<int, string> $modules
     * @return array<int, string>
     */
    private static function buildModulePermissions(string $business, array $modules): array
    {
        $permissions = [];

        foreach ($modules as $module) {
            $permissions[] = "{$business}.{$module}.view";
            $permissions[] = "{$business}.{$module}.manage";
        }

        return $permissions;
    }
}
