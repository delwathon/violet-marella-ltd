<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data (optional - comment out if you want to keep existing data)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('studio_payments')->truncate();
        DB::table('studio_sessions')->truncate();
        DB::table('studio_customers')->truncate();
        DB::table('studio_rooms')->truncate();
        DB::table('studio_categories')->truncate();
        DB::table('studio_settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeding Photo Studio V2...');

        // 1. Seed Settings
        $this->seedSettings();
        $this->command->info('✓ Settings seeded');

        // 2. Seed Categories
        $categoryIds = $this->seedCategories();
        $this->command->info('✓ Categories seeded');

        // 3. Seed Rooms (Optional)
        $this->seedRooms($categoryIds);
        $this->command->info('✓ Rooms seeded');

        // 4. Seed Customers
        $customerIds = $this->seedCustomers();
        $this->command->info('✓ Customers seeded');

        // 5. Seed Sessions
        $sessionIds = $this->seedSessions($categoryIds, $customerIds);
        $this->command->info('✓ Sessions seeded');

        // 6. Seed Payments
        $this->seedPayments($sessionIds);
        $this->command->info('✓ Payments seeded');

        $this->command->info('🎉 Photo Studio V2 seeding completed successfully!');
    }

    /**
     * Seed studio settings
     */
    private function seedSettings()
    {
        DB::table('studio_settings')->insert([
            [
                'key' => 'offset_time',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Preparation time before session timer starts (in minutes)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_base_time',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Default base session duration (in minutes)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_base_price',
                'value' => '30000',
                'type' => 'integer',
                'description' => 'Default base price for sessions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'allow_overtime',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow sessions to go into overtime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => '₦',
                'type' => 'string',
                'description' => 'Currency symbol for display',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Seed studio categories
     */
    private function seedCategories()
    {
        $categories = [
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Our standard studio space perfect for individual shoots, couples, and small groups. Equipped with professional lighting and backdrops.',
                'color' => '#3b82f6',
                'base_time' => 30,
                'base_price' => 30000.00,
                'per_minute_rate' => 1000.00,
                'hourly_rate' => 60000.00,
                'max_occupants' => 4,
                'max_concurrent_sessions' => 3,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deluxe',
                'slug' => 'deluxe',
                'description' => 'Premium studio space with extended room size and advanced equipment. Ideal for larger groups, family portraits, and professional shoots.',
                'color' => '#8b5cf6',
                'base_time' => 30,
                'base_price' => 50000.00,
                'per_minute_rate' => 1666.67,
                'hourly_rate' => 100000.00,
                'max_occupants' => 6,
                'max_concurrent_sessions' => 2,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Luxury studio suite with all premium amenities. Features high-end equipment, multiple backdrop options, and spacious layout.',
                'color' => '#ec4899',
                'base_time' => 60,
                'base_price' => 80000.00,
                'per_minute_rate' => 1333.33,
                'hourly_rate' => 80000.00,
                'max_occupants' => 8,
                'max_concurrent_sessions' => 1,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $ids = [];
        foreach ($categories as $category) {
            $ids[] = DB::table('studio_categories')->insertGetId($category);
        }

        return $ids;
    }

    /**
     * Seed studio rooms (optional physical rooms)
     */
    private function seedRooms($categoryIds)
    {
        $rooms = [
            // Classic Category Rooms
            [
                'category_id' => $categoryIds[0],
                'name' => 'Studio A',
                'code' => 'STDA',
                'description' => 'Ground floor classic studio with natural lighting',
                'floor' => 0,
                'location' => 'Ground Floor - Left Wing',
                'size_sqm' => 40,
                'equipment' => json_encode(['Professional Camera', 'Ring Light', 'Softbox', 'Backdrop Stand', 'Props Cabinet']),
                'features' => json_encode(['Natural Light', 'Air Conditioning', 'Sound System', 'WiFi']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categoryIds[0],
                'name' => 'Studio B',
                'code' => 'STDB',
                'description' => 'Ground floor classic studio with controlled lighting',
                'floor' => 0,
                'location' => 'Ground Floor - Right Wing',
                'size_sqm' => 40,
                'equipment' => json_encode(['Professional Camera', 'LED Panel', 'Umbrella Light', 'Backdrop Stand']),
                'features' => json_encode(['Blackout Curtains', 'Air Conditioning', 'WiFi']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categoryIds[0],
                'name' => 'Studio C',
                'code' => 'STDC',
                'description' => 'First floor classic studio',
                'floor' => 1,
                'location' => 'First Floor - Center',
                'size_sqm' => 45,
                'equipment' => json_encode(['Professional Camera', 'Strobe Lights', 'Reflectors', 'Backdrop Stand']),
                'features' => json_encode(['Large Windows', 'Air Conditioning', 'WiFi', 'Makeup Area']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Deluxe Category Rooms
            [
                'category_id' => $categoryIds[1],
                'name' => 'Deluxe Suite 1',
                'code' => 'DLXS1',
                'description' => 'Premium deluxe studio with extended space',
                'floor' => 1,
                'location' => 'First Floor - East Wing',
                'size_sqm' => 60,
                'equipment' => json_encode(['Pro DSLR Camera', 'Strobe Lights Set', 'Continuous Lights', 'Multiple Backdrops', 'Props Collection', 'Fog Machine']),
                'features' => json_encode(['Large Space', 'Air Conditioning', 'WiFi', 'Private Restroom', 'Makeup Station', 'Waiting Area']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categoryIds[1],
                'name' => 'Deluxe Suite 2',
                'code' => 'DLXS2',
                'description' => 'Premium deluxe studio with advanced equipment',
                'floor' => 1,
                'location' => 'First Floor - West Wing',
                'size_sqm' => 65,
                'equipment' => json_encode(['Pro Camera System', 'Advanced Lighting Kit', 'Green Screen', 'Multiple Backdrops', 'Video Equipment']),
                'features' => json_encode(['Extra Large Space', 'Air Conditioning', 'WiFi', 'Private Restroom', 'VIP Lounge', 'Refreshments']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Premium Category Room
            [
                'category_id' => $categoryIds[2],
                'name' => 'Premium Loft',
                'code' => 'PREM1',
                'description' => 'Luxury premium studio with all amenities',
                'floor' => 2,
                'location' => 'Second Floor - Penthouse',
                'size_sqm' => 100,
                'equipment' => json_encode(['Pro Camera Suite', 'Professional Lighting System', 'Multiple Backdrops', 'Green Screen', 'Video Production Setup', 'Props Library', 'Special Effects Equipment']),
                'features' => json_encode(['Spacious Layout', 'Air Conditioning', 'WiFi', 'Private Facilities', 'VIP Lounge', 'Kitchen Area', 'Terrace Access', 'Premium Finishes']),
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('studio_rooms')->insert($room);
        }
    }

    /**
     * Seed studio customers
     */
    private function seedCustomers()
    {
        $customers = [
            [
                'name' => 'Adewale Johnson',
                'email' => 'adewale.johnson@example.com',
                'phone' => '08012345678',
                'address' => '15 Victoria Island Road, Lagos',
                'date_of_birth' => '1990-05-15',
                'total_sessions' => 12,
                'total_spent' => 480000.00,
                'last_visit' => Carbon::now()->subDays(2),
                'notes' => 'Regular customer, prefers Classic studio. Professional photographer.',
                'preferences' => json_encode(['preferred_category' => 'Classic', 'preferred_time' => 'Morning', 'special_requests' => 'Extra lighting']),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chiamaka Okonkwo',
                'email' => 'chiamaka.o@example.com',
                'phone' => '08098765432',
                'address' => '42 Lekki Phase 1, Lagos',
                'date_of_birth' => '1988-11-20',
                'total_sessions' => 8,
                'total_spent' => 320000.00,
                'last_visit' => Carbon::now()->subDays(7),
                'notes' => 'Fashion influencer, books Deluxe for content creation.',
                'preferences' => json_encode(['preferred_category' => 'Deluxe', 'preferred_time' => 'Afternoon']),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ibrahim Musa',
                'email' => 'ibrahim.musa@example.com',
                'phone' => '07012345678',
                'address' => '23 Ikoyi Crescent, Lagos',
                'date_of_birth' => '1985-03-10',
                'total_sessions' => 5,
                'total_spent' => 250000.00,
                'last_visit' => Carbon::now()->subDays(15),
                'notes' => 'Corporate client, books for team photoshoots.',
                'preferences' => json_encode(['preferred_category' => 'Deluxe', 'invoice_required' => true]),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(3),
                'updated_at' => now(),
            ],
            [
                'name' => 'Funmilayo Adebayo',
                'email' => 'funmi.adebayo@example.com',
                'phone' => '08156789012',
                'address' => '8 Allen Avenue, Ikeja, Lagos',
                'date_of_birth' => '1995-08-25',
                'total_sessions' => 15,
                'total_spent' => 450000.00,
                'last_visit' => Carbon::now()->subDays(1),
                'notes' => 'VIP customer, frequently books for family events.',
                'preferences' => json_encode(['preferred_category' => 'Classic', 'loyalty_member' => true]),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(8),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chinedu Okoro',
                'email' => 'chinedu.okoro@example.com',
                'phone' => '09087654321',
                'address' => '67 Surulere Road, Lagos',
                'date_of_birth' => '1992-01-30',
                'total_sessions' => 3,
                'total_spent' => 90000.00,
                'last_visit' => Carbon::now()->subDays(30),
                'notes' => 'New customer, first experience was excellent.',
                'preferences' => json_encode(['preferred_category' => 'Classic']),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blessing Eze',
                'email' => 'blessing.eze@example.com',
                'phone' => '08134567890',
                'address' => '12 Ajah Estate, Lagos',
                'date_of_birth' => '1993-07-12',
                'total_sessions' => 20,
                'total_spent' => 800000.00,
                'last_visit' => Carbon::now()->subHours(6),
                'notes' => 'Platinum tier customer, books Premium suite regularly.',
                'preferences' => json_encode(['preferred_category' => 'Premium', 'vip_service' => true, 'advance_booking' => true]),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subYear(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oluwaseun Balogun',
                'email' => 'seun.balogun@example.com',
                'phone' => '07098765432',
                'address' => '34 Maryland, Lagos',
                'date_of_birth' => '1991-04-18',
                'total_sessions' => 6,
                'total_spent' => 180000.00,
                'last_visit' => Carbon::now()->subDays(20),
                'notes' => 'Event planner, books for client photoshoots.',
                'preferences' => json_encode(['preferred_category' => 'Deluxe', 'corporate_account' => true]),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(5),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amina Abdullahi',
                'email' => 'amina.a@example.com',
                'phone' => '08176543210',
                'address' => '56 Yaba Road, Lagos',
                'date_of_birth' => '1989-12-05',
                'total_sessions' => 10,
                'total_spent' => 350000.00,
                'last_visit' => Carbon::now()->subDays(10),
                'notes' => 'Professional model, prefers early morning sessions.',
                'preferences' => json_encode(['preferred_category' => 'Deluxe', 'preferred_time' => 'Early Morning', 'makeup_artist_needed' => true]),
                'is_active' => true,
                'is_blacklisted' => false,
                'created_at' => now()->subMonths(7),
                'updated_at' => now(),
            ],
        ];

        $ids = [];
        foreach ($customers as $customer) {
            $ids[] = DB::table('studio_customers')->insertGetId($customer);
        }

        return $ids;
    }

    /**
     * Seed studio sessions
     */
    private function seedSessions($categoryIds, $customerIds)
    {
        $sessions = [];
        $sessionIds = [];

        // Create completed sessions (last 30 days)
        for ($i = 0; $i < 25; $i++) {
            $categoryId = $categoryIds[array_rand($categoryIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            
            $category = DB::table('studio_categories')->where('id', $categoryId)->first();
            
            $checkInTime = Carbon::now()->subDays(rand(1, 30))->setTime(rand(9, 18), rand(0, 59));
            $offsetTime = 10;
            $scheduledStartTime = $checkInTime->copy()->addMinutes($offsetTime);
            $actualStartTime = $scheduledStartTime->copy()->addMinutes(rand(0, 5));
            
            $bookedDuration = [30, 60, 90, 120][array_rand([30, 60, 90, 120])];
            $actualDuration = $bookedDuration + rand(-10, 30);
            $overtimeDuration = max(0, $actualDuration - $bookedDuration);
            
            $checkOutTime = $actualStartTime->copy()->addMinutes($actualDuration);
            
            // Calculate amounts
            $baseAmount = $category->base_price;
            $overtimeAmount = $overtimeDuration * $category->per_minute_rate;
            $totalAmount = $baseAmount + $overtimeAmount;
            $discountAmount = rand(0, 1) ? 0 : rand(1000, 5000);
            $finalAmount = $totalAmount - $discountAmount;
            
            $sessions[] = [
                'category_id' => $categoryId,
                'customer_id' => $customerId,
                'session_code' => 'SS-' . strtoupper(uniqid()),
                'qr_code' => 'QR-' . time() . '-' . rand(1000, 9999),
                'number_of_people' => rand(1, $category->max_occupants),
                'party_names' => json_encode(['Guest ' . rand(1, 5), 'Guest ' . rand(6, 10)]),
                'check_in_time' => $checkInTime,
                'scheduled_start_time' => $scheduledStartTime,
                'actual_start_time' => $actualStartTime,
                'check_out_time' => $checkOutTime,
                'booked_duration' => $bookedDuration,
                'offset_time_applied' => $offsetTime,
                'actual_duration' => $actualDuration,
                'overtime_duration' => $overtimeDuration,
                'rate_base_time' => $category->base_time,
                'rate_base_price' => $category->base_price,
                'rate_per_minute' => $category->per_minute_rate,
                'base_amount' => $baseAmount,
                'overtime_amount' => $overtimeAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $finalAmount,
                'payment_status' => ['paid', 'paid', 'paid', 'partial'][array_rand(['paid', 'paid', 'paid', 'partial'])],
                'payment_method' => ['cash', 'card', 'transfer'][array_rand(['cash', 'card', 'transfer'])],
                'amount_paid' => $finalAmount,
                'balance' => 0,
                'status' => 'completed',
                'notes' => null,
                'created_by' => 1,
                'checked_out_by' => 1,
                'created_at' => $checkInTime,
                'updated_at' => $checkOutTime,
            ];
        }

        // Create some active sessions (today)
        for ($i = 0; $i < 3; $i++) {
            $categoryId = $categoryIds[array_rand($categoryIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            
            $category = DB::table('studio_categories')->where('id', $categoryId)->first();
            
            $checkInTime = Carbon::now()->subHours(rand(1, 3));
            $offsetTime = 10;
            $scheduledStartTime = $checkInTime->copy()->addMinutes($offsetTime);
            $actualStartTime = $scheduledStartTime->copy();
            
            $bookedDuration = [30, 60][array_rand([30, 60])];
            $actualDuration = Carbon::now()->diffInMinutes($actualStartTime);
            $overtimeDuration = max(0, $actualDuration - $bookedDuration);
            
            $baseAmount = $category->base_price;
            $overtimeAmount = $overtimeDuration * $category->per_minute_rate;
            $totalAmount = $baseAmount + $overtimeAmount;
            
            $status = $actualDuration > $bookedDuration ? 'overtime' : 'active';
            
            $sessions[] = [
                'category_id' => $categoryId,
                'customer_id' => $customerId,
                'session_code' => 'SS-' . strtoupper(uniqid()),
                'qr_code' => 'QR-' . time() . '-' . rand(1000, 9999),
                'number_of_people' => rand(1, $category->max_occupants),
                'party_names' => json_encode(['Active Guest ' . rand(1, 5)]),
                'check_in_time' => $checkInTime,
                'scheduled_start_time' => $scheduledStartTime,
                'actual_start_time' => $actualStartTime,
                'check_out_time' => null,
                'booked_duration' => $bookedDuration,
                'offset_time_applied' => $offsetTime,
                'actual_duration' => null,
                'overtime_duration' => null,
                'rate_base_time' => $category->base_time,
                'rate_base_price' => $category->base_price,
                'rate_per_minute' => $category->per_minute_rate,
                'base_amount' => $baseAmount,
                'overtime_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'payment_method' => null,
                'amount_paid' => 0,
                'balance' => $totalAmount,
                'status' => $status,
                'notes' => null,
                'created_by' => 1,
                'checked_out_by' => null,
                'created_at' => $checkInTime,
                'updated_at' => now(),
            ];
        }

        // Create a pending session (waiting for offset time)
        $categoryId = $categoryIds[0];
        $customerId = $customerIds[0];
        $category = DB::table('studio_categories')->where('id', $categoryId)->first();
        
        $checkInTime = Carbon::now()->subMinutes(5);
        $offsetTime = 10;
        $scheduledStartTime = $checkInTime->copy()->addMinutes($offsetTime);
        
        $sessions[] = [
            'category_id' => $categoryId,
            'customer_id' => $customerId,
            'session_code' => 'SS-' . strtoupper(uniqid()),
            'qr_code' => 'QR-' . time() . '-' . rand(1000, 9999),
            'number_of_people' => 2,
            'party_names' => json_encode(['Pending Guest 1', 'Pending Guest 2']),
            'check_in_time' => $checkInTime,
            'scheduled_start_time' => $scheduledStartTime,
            'actual_start_time' => null,
            'check_out_time' => null,
            'booked_duration' => 30,
            'offset_time_applied' => $offsetTime,
            'actual_duration' => null,
            'overtime_duration' => null,
            'rate_base_time' => $category->base_time,
            'rate_base_price' => $category->base_price,
            'rate_per_minute' => $category->per_minute_rate,
            'base_amount' => $category->base_price,
            'overtime_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $category->base_price,
            'payment_status' => 'pending',
            'payment_method' => null,
            'amount_paid' => 0,
            'balance' => $category->base_price,
            'status' => 'pending',
            'notes' => 'Waiting for prep time to complete',
            'created_by' => 1,
            'checked_out_by' => null,
            'created_at' => $checkInTime,
            'updated_at' => now(),
        ];

        foreach ($sessions as $session) {
            $sessionIds[] = DB::table('studio_sessions')->insertGetId($session);
        }

        return $sessionIds;
    }

    /**
     * Seed studio payments
     */
    private function seedPayments($sessionIds)
    {
        $completedSessions = DB::table('studio_sessions')
            ->whereIn('id', $sessionIds)
            ->where('status', 'completed')
            ->get();

        foreach ($completedSessions as $session) {
            // Full payment
            DB::table('studio_payments')->insert([
                'session_id' => $session->id,
                'reference' => 'PAY-' . strtoupper(uniqid()),
                'amount' => $session->total_amount,
                'payment_method' => $session->payment_method,
                'payment_type' => 'full',
                'transaction_reference' => 'TXN-' . time() . '-' . rand(1000, 9999),
                'payment_date' => $session->check_out_time,
                'status' => 'completed',
                'notes' => 'Full payment at checkout',
                'received_by' => 1,
                'created_at' => $session->check_out_time,
                'updated_at' => $session->check_out_time,
            ]);

            // Occasionally add a partial payment scenario
            if (rand(1, 10) > 8) {
                $partialAmount = $session->total_amount * 0.5;
                $remainingAmount = $session->total_amount - $partialAmount;
                
                // First partial payment
                DB::table('studio_payments')->insert([
                    'session_id' => $session->id,
                    'reference' => 'PAY-' . strtoupper(uniqid()),
                    'amount' => $partialAmount,
                    'payment_method' => 'cash',
                    'payment_type' => 'partial',
                    'transaction_reference' => 'TXN-' . time() . '-' . rand(1000, 9999),
                    'payment_date' => Carbon::parse($session->check_in_time)->addMinutes(5),
                    'status' => 'completed',
                    'notes' => 'Partial payment (50%)',
                    'received_by' => 1,
                    'created_at' => $session->check_in_time,
                    'updated_at' => $session->check_in_time,
                ]);
                
                // Second partial payment
                DB::table('studio_payments')->insert([
                    'session_id' => $session->id,
                    'reference' => 'PAY-' . strtoupper(uniqid()),
                    'amount' => $remainingAmount,
                    'payment_method' => $session->payment_method,
                    'payment_type' => 'partial',
                    'transaction_reference' => 'TXN-' . time() . '-' . rand(1000, 9999),
                    'payment_date' => $session->check_out_time,
                    'status' => 'completed',
                    'notes' => 'Remaining payment at checkout',
                    'received_by' => 1,
                    'created_at' => $session->check_out_time,
                    'updated_at' => $session->check_out_time,
                ]);
            }
        }
    }
}