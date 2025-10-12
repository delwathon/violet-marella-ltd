<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Store Information
            [
                'key' => 'store_name',
                'value' => 'Violet Marella Limited',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Name of the store',
                'is_public' => true,
            ],
            [
                'key' => 'store_address',
                'value' => '123 Main Street, Lagos, Nigeria',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Store address',
                'is_public' => true,
            ],
            [
                'key' => 'store_phone',
                'value' => '+234 800 000 0000',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Store phone number',
                'is_public' => true,
            ],
            [
                'key' => 'store_email',
                'value' => 'info@violetmarella.com',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Store email address',
                'is_public' => true,
            ],
            [
                'key' => 'store_currency',
                'value' => 'NGN',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Store currency code',
                'is_public' => true,
            ],
            [
                'key' => 'store_currency_symbol',
                'value' => '₦',
                'type' => 'string',
                'group' => 'store',
                'description' => 'Store currency symbol',
                'is_public' => true,
            ],

            // Tax Settings
            [
                'key' => 'default_tax_rate',
                'value' => '7.5',
                'type' => 'float',
                'group' => 'tax',
                'description' => 'Default tax rate percentage',
                'is_public' => false,
            ],
            [
                'key' => 'tax_inclusive_pricing',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'tax',
                'description' => 'Whether prices include tax',
                'is_public' => false,
            ],

            // Payment Settings
            [
                'key' => 'accepted_payment_methods',
                'value' => json_encode(['cash', 'card', 'transfer']),
                'type' => 'json',
                'group' => 'payment',
                'description' => 'Accepted payment methods',
                'is_public' => false,
            ],
            [
                'key' => 'minimum_cash_drawer_amount',
                'value' => '5000',
                'type' => 'float',
                'group' => 'payment',
                'description' => 'Minimum cash drawer amount',
                'is_public' => false,
            ],

            // Receipt Settings
            [
                'key' => 'receipt_footer_message',
                'value' => 'Thank you for shopping with us!',
                'type' => 'string',
                'group' => 'receipt',
                'description' => 'Footer message on receipts',
                'is_public' => true,
            ],
            [
                'key' => 'receipt_show_tax_breakdown',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'receipt',
                'description' => 'Show tax breakdown on receipts',
                'is_public' => false,
            ],
            [
                'key' => 'receipt_printer_name',
                'value' => 'POS-58',
                'type' => 'string',
                'group' => 'receipt',
                'description' => 'Default receipt printer name',
                'is_public' => false,
            ],

            // Inventory Settings
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'integer',
                'group' => 'inventory',
                'description' => 'Default low stock threshold',
                'is_public' => false,
            ],
            [
                'key' => 'track_inventory',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Enable inventory tracking',
                'is_public' => false,
            ],

            // Loyalty Settings
            [
                'key' => 'loyalty_points_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'loyalty',
                'description' => 'Enable loyalty points system',
                'is_public' => false,
            ],
            [
                'key' => 'loyalty_points_rate',
                'value' => '1',
                'type' => 'float',
                'group' => 'loyalty',
                'description' => 'Points earned per currency unit spent',
                'is_public' => false,
            ],
            [
                'key' => 'loyalty_discount_rate',
                'value' => '5',
                'type' => 'float',
                'group' => 'loyalty',
                'description' => 'Discount percentage for loyalty customers',
                'is_public' => false,
            ],

            // System Settings
            [
                'key' => 'backup_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable automatic backups',
                'is_public' => false,
            ],
            [
                'key' => 'auto_logout_minutes',
                'value' => '30',
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Auto logout after inactivity (minutes)',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
