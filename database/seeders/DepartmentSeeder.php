<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Management', 'icon' => 'user-tie', 'color' => 'primary'],
            ['name' => 'Sales', 'icon' => 'chart-line', 'color' => 'success'],
            ['name' => 'Operations', 'icon' => 'cogs', 'color' => 'info'],
            ['name' => 'Finance', 'icon' => 'dollar-sign', 'color' => 'warning'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['slug' => Str::slug($department['name'])],
                [
                    'name' => $department['name'],
                    'slug' => Str::slug($department['name']),
                    'description' => $department['name'] . ' department',
                    'icon' => $department['icon'],
                    'color' => $department['color'],
                    'is_active' => true,
                ]
            );
        }
    }
}
