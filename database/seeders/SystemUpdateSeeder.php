<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemUpdateSeeder extends Seeder
{
    /**
     * Seed only idempotent core system data during in-app updates.
     */
    public function run(): void
    {
        $this->call([
            BusinessSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
