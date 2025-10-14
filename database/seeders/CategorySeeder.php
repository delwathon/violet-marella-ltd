<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Groceries',
                'slug' => 'groceries',
                'description' => 'Essential food items and household staples',
                'color' => '#28a745',
                'sort_order' => 1,
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'description' => 'Drinks, juices, and liquid refreshments',
                'color' => '#007bff',
                'sort_order' => 2,
            ],
            [
                'name' => 'Snacks',
                'slug' => 'snacks',
                'description' => 'Chips, cookies, and quick bites',
                'color' => '#ffc107',
                'sort_order' => 3,
            ],
            [
                'name' => 'Household',
                'slug' => 'household',
                'description' => 'Cleaning supplies and home essentials',
                'color' => '#6f42c1',
                'sort_order' => 4,
            ],
            [
                'name' => 'Personal Care',
                'slug' => 'personal-care',
                'description' => 'Health and beauty products',
                'color' => '#e83e8c',
                'sort_order' => 5,
            ],
            [
                'name' => 'Dairy Products',
                'slug' => 'dairy',
                'description' => 'Milk, cheese, yogurt, and dairy items',
                'color' => '#17a2b8',
                'sort_order' => 6,
            ],
            [
                'name' => 'Frozen Foods',
                'slug' => 'frozen',
                'description' => 'Frozen meals, ice cream, and frozen goods',
                'color' => '#20c997',
                'sort_order' => 7,
            ],
            [
                'name' => 'Bakery',
                'slug' => 'bakery',
                'description' => 'Fresh bread, pastries, and baked goods',
                'color' => '#fd7e14',
                'sort_order' => 8,
            ],
            [
                'name' => 'Fruits & Vegetables',
                'slug' => 'produce',
                'description' => 'Fresh fruits and vegetables',
                'color' => '#198754',
                'sort_order' => 9,
            ],
            [
                'name' => 'Meat & Seafood',
                'slug' => 'meat-seafood',
                'description' => 'Fresh and frozen meat and seafood',
                'color' => '#dc3545',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
