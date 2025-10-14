<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $groceries = Category::where('slug', 'groceries')->first();
        $beverages = Category::where('slug', 'beverages')->first();
        $snacks = Category::where('slug', 'snacks')->first();
        $dairy = Category::where('slug', 'dairy')->first();
        $household = Category::where('slug', 'household')->first();
        $personalCare = Category::where('slug', 'personal-care')->first();
        $bakery = Category::where('slug', 'bakery')->first();

        $products = [
            // Groceries
            [
                'name' => 'Rice (5kg)',
                'sku' => 'RICE-5KG-001',
                'barcode' => '1234567890123',
                'description' => 'Premium long grain rice',
                'category_id' => $groceries->id,
                'price' => 2500.00,
                'cost_price' => 2000.00,
                'wholesale_price' => 2200.00,
                'stock_quantity' => 50,
                'minimum_stock_level' => 10,
                'unit' => 'bag',
                'brand' => 'Royal Stallion',
                'supplier' => 'Nigerian Rice Mills',
                'is_featured' => true,
            ],
            [
                'name' => 'Beans (1kg)',
                'sku' => 'BEANS-1KG-001',
                'barcode' => '1234567890124',
                'description' => 'Brown beans',
                'category_id' => $groceries->id,
                'price' => 800.00,
                'cost_price' => 600.00,
                'stock_quantity' => 30,
                'minimum_stock_level' => 5,
                'unit' => 'bag',
                'brand' => 'Local',
            ],
            [
                'name' => 'Cooking Oil (1L)',
                'sku' => 'OIL-1L-001',
                'barcode' => '1234567890125',
                'description' => 'Vegetable cooking oil',
                'category_id' => $groceries->id,
                'price' => 1200.00,
                'cost_price' => 900.00,
                'stock_quantity' => 40,
                'minimum_stock_level' => 8,
                'unit' => 'bottle',
                'brand' => 'Kings Oil',
            ],

            // Beverages
            [
                'name' => 'Coca Cola (50cl)',
                'sku' => 'COKE-50CL-001',
                'barcode' => '1234567890126',
                'description' => 'Coca Cola soft drink',
                'category_id' => $beverages->id,
                'price' => 150.00,
                'cost_price' => 120.00,
                'stock_quantity' => 100,
                'minimum_stock_level' => 20,
                'unit' => 'bottle',
                'brand' => 'Coca Cola',
                'is_featured' => true,
            ],
            [
                'name' => 'Orange Juice (1L)',
                'sku' => 'OJUICE-1L-001',
                'barcode' => '1234567890127',
                'description' => 'Fresh orange juice',
                'category_id' => $beverages->id,
                'price' => 800.00,
                'cost_price' => 600.00,
                'stock_quantity' => 25,
                'minimum_stock_level' => 5,
                'unit' => 'bottle',
                'brand' => 'Chi Limited',
            ],
            [
                'name' => 'Water (50cl)',
                'sku' => 'WATER-50CL-001',
                'barcode' => '1234567890128',
                'description' => 'Pure water',
                'category_id' => $beverages->id,
                'price' => 50.00,
                'cost_price' => 35.00,
                'stock_quantity' => 200,
                'minimum_stock_level' => 50,
                'unit' => 'bottle',
                'brand' => 'Aquafina',
            ],

            // Snacks
            [
                'name' => 'Chips (150g)',
                'sku' => 'CHIPS-150G-001',
                'barcode' => '1234567890129',
                'description' => 'Potato chips',
                'category_id' => $snacks->id,
                'price' => 300.00,
                'cost_price' => 220.00,
                'stock_quantity' => 60,
                'minimum_stock_level' => 15,
                'unit' => 'pack',
                'brand' => 'Lays',
            ],
            [
                'name' => 'Biscuits (200g)',
                'sku' => 'BISC-200G-001',
                'barcode' => '1234567890130',
                'description' => 'Sweet biscuits',
                'category_id' => $snacks->id,
                'price' => 250.00,
                'cost_price' => 180.00,
                'stock_quantity' => 45,
                'minimum_stock_level' => 10,
                'unit' => 'pack',
                'brand' => 'McVities',
            ],

            // Dairy
            [
                'name' => 'Fresh Milk (1L)',
                'sku' => 'MILK-1L-001',
                'barcode' => '1234567890131',
                'description' => 'Fresh cow milk',
                'category_id' => $dairy->id,
                'price' => 600.00,
                'cost_price' => 450.00,
                'stock_quantity' => 35,
                'minimum_stock_level' => 8,
                'unit' => 'bottle',
                'brand' => 'Peak Milk',
                'expiry_date' => now()->addDays(7),
            ],
            [
                'name' => 'Cheese (200g)',
                'sku' => 'CHEESE-200G-001',
                'barcode' => '1234567890132',
                'description' => 'Cheddar cheese',
                'category_id' => $dairy->id,
                'price' => 1200.00,
                'cost_price' => 900.00,
                'stock_quantity' => 20,
                'minimum_stock_level' => 5,
                'unit' => 'pack',
                'brand' => 'Laughing Cow',
                'expiry_date' => now()->addDays(30),
            ],

            // Household
            [
                'name' => 'Detergent (500g)',
                'sku' => 'DET-500G-001',
                'barcode' => '1234567890133',
                'description' => 'Laundry detergent',
                'category_id' => $household->id,
                'price' => 800.00,
                'cost_price' => 600.00,
                'stock_quantity' => 30,
                'minimum_stock_level' => 8,
                'unit' => 'pack',
                'brand' => 'Ariel',
            ],
            [
                'name' => 'Toilet Paper (4 rolls)',
                'sku' => 'TP-4RL-001',
                'barcode' => '1234567890134',
                'description' => 'Soft toilet paper',
                'category_id' => $household->id,
                'price' => 600.00,
                'cost_price' => 450.00,
                'stock_quantity' => 40,
                'minimum_stock_level' => 10,
                'unit' => 'pack',
                'brand' => 'Soft Touch',
            ],

            // Personal Care
            [
                'name' => 'Toothpaste (150g)',
                'sku' => 'TPASTE-150G-001',
                'barcode' => '1234567890135',
                'description' => 'Fluoride toothpaste',
                'category_id' => $personalCare->id,
                'price' => 500.00,
                'cost_price' => 350.00,
                'stock_quantity' => 25,
                'minimum_stock_level' => 8,
                'unit' => 'tube',
                'brand' => 'Colgate',
            ],
            [
                'name' => 'Shampoo (400ml)',
                'sku' => 'SHAMPOO-400ML-001',
                'barcode' => '1234567890136',
                'description' => 'Anti-dandruff shampoo',
                'category_id' => $personalCare->id,
                'price' => 1200.00,
                'cost_price' => 900.00,
                'stock_quantity' => 20,
                'minimum_stock_level' => 5,
                'unit' => 'bottle',
                'brand' => 'Head & Shoulders',
            ],

            // Bakery
            [
                'name' => 'Bread (500g)',
                'sku' => 'BREAD-500G-001',
                'barcode' => '1234567890137',
                'description' => 'Fresh white bread',
                'category_id' => $bakery->id,
                'price' => 400.00,
                'cost_price' => 280.00,
                'stock_quantity' => 15,
                'minimum_stock_level' => 5,
                'unit' => 'loaf',
                'brand' => 'Local Bakery',
                'expiry_date' => now()->addDays(3),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
