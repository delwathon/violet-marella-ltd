<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Studio;
use App\Models\StudioRate;
use App\Models\StudioCustomer;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Default Rate
        StudioRate::create([
            'name' => 'Standard Rate',
            'base_time' => 30,
            'base_amount' => 2000.00,
            'per_minute_rate' => 66.67,
            'hourly_rate' => 4000.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        // Create Additional Rates
        StudioRate::create([
            'name' => 'Premium Rate',
            'base_time' => 60,
            'base_amount' => 5000.00,
            'per_minute_rate' => 83.33,
            'hourly_rate' => 5000.00,
            'is_default' => false,
            'is_active' => true,
        ]);

        StudioRate::create([
            'name' => 'Budget Rate',
            'base_time' => 30,
            'base_amount' => 1500.00,
            'per_minute_rate' => 50.00,
            'hourly_rate' => 3000.00,
            'is_default' => false,
            'is_active' => true,
        ]);

        // Create Studios
        $studios = [
            [
                'name' => 'Studio A',
                'code' => 'STUDIO-A',
                'description' => 'Professional photo studio with full lighting setup',
                'status' => 'available',
                'studio_rate_id' => 1,
                'capacity' => 2,
                'equipment' => json_encode([
                    'DSLR Camera',
                    'Ring Light',
                    'Softbox Lighting',
                    'Backdrop Stand',
                    'Props Collection'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Studio B',
                'code' => 'STUDIO-B',
                'description' => 'Compact studio ideal for individual sessions',
                'status' => 'available',
                'studio_rate_id' => 3,
                'capacity' => 1,
                'equipment' => json_encode([
                    'Mirrorless Camera',
                    'LED Panel Lights',
                    'Green Screen',
                    'Tripod'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Studio C',
                'code' => 'STUDIO-C',
                'description' => 'Large studio with natural lighting',
                'status' => 'available',
                'studio_rate_id' => 2,
                'capacity' => 4,
                'equipment' => json_encode([
                    'Professional Camera Setup',
                    'Multiple Backdrop Options',
                    'Studio Lighting Kit',
                    'Reflectors',
                    'Props and Accessories'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Studio D',
                'code' => 'STUDIO-D',
                'description' => 'Outdoor-themed studio space',
                'status' => 'maintenance',
                'studio_rate_id' => 1,
                'capacity' => 2,
                'equipment' => json_encode([
                    'Camera Equipment',
                    'Portable Lighting',
                    'Natural Props'
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($studios as $studio) {
            Studio::create($studio);
        }

        // Create Sample Customers
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+234 801 234 5678',
                'address' => '15 Allen Avenue, Ikeja, Lagos',
                'date_of_birth' => '1990-05-15',
                'total_sessions' => 0,
                'total_spent' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'phone' => '+234 802 345 6789',
                'address' => '23 Victoria Island, Lagos',
                'date_of_birth' => '1995-08-22',
                'total_sessions' => 0,
                'total_spent' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Michael Okonkwo',
                'email' => 'michael.o@example.com',
                'phone' => '+234 803 456 7890',
                'address' => '45 Lekki Phase 1, Lagos',
                'date_of_birth' => '1988-12-10',
                'total_sessions' => 0,
                'total_spent' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Grace Adeyemi',
                'email' => 'grace.a@example.com',
                'phone' => '+234 804 567 8901',
                'address' => '12 Surulere, Lagos',
                'date_of_birth' => '1992-03-18',
                'total_sessions' => 0,
                'total_spent' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'David Eze',
                'email' => 'david.eze@example.com',
                'phone' => '+234 805 678 9012',
                'address' => '8 Ajah, Lagos',
                'date_of_birth' => '1985-07-25',
                'total_sessions' => 0,
                'total_spent' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            StudioCustomer::create($customer);
        }

        $this->command->info('Studios, rates, and sample customers seeded successfully!');
    }
}