<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prop;
use App\Models\RentalCustomer;
use App\Models\PropRental;
use Carbon\Carbon;

class PropRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Props
        $props = [
            [
                'name' => 'Acoustic Guitar - Yamaha FG830',
                'category' => 'guitars',
                'type' => 'Acoustic Guitar',
                'brand' => 'Yamaha',
                'model' => 'FG830',
                'daily_rate' => 1500.00,
                'status' => 'available',
                'condition' => 'excellent',
                'description' => 'Full-size acoustic guitar with solid spruce top',
                'image' => 'fas fa-guitar',
                'serial_number' => 'YAM-001',
                'purchase_date' => Carbon::parse('2023-01-15'),
                'last_maintenance' => Carbon::parse('2024-01-10'),
            ],
            [
                'name' => 'Electric Guitar - Fender Stratocaster',
                'category' => 'guitars',
                'type' => 'Electric Guitar',
                'brand' => 'Fender',
                'model' => 'Stratocaster',
                'daily_rate' => 2000.00,
                'status' => 'available',
                'condition' => 'good',
                'description' => 'Classic electric guitar with maple neck',
                'image' => 'fas fa-guitar',
                'serial_number' => 'FEN-002',
                'purchase_date' => Carbon::parse('2023-02-20'),
                'last_maintenance' => Carbon::parse('2024-01-05'),
            ],
            [
                'name' => 'Digital Piano - Yamaha P-45',
                'category' => 'keyboards',
                'type' => 'Digital Piano',
                'brand' => 'Yamaha',
                'model' => 'P-45',
                'daily_rate' => 2500.00,
                'status' => 'available',
                'condition' => 'excellent',
                'description' => '88-key weighted digital piano',
                'image' => 'fas fa-piano',
                'serial_number' => 'YAM-003',
                'purchase_date' => Carbon::parse('2023-03-10'),
                'last_maintenance' => Carbon::parse('2024-01-08'),
            ],
            [
                'name' => 'Drum Kit - Pearl Export',
                'category' => 'drums',
                'type' => 'Drum Kit',
                'brand' => 'Pearl',
                'model' => 'Export',
                'daily_rate' => 3000.00,
                'status' => 'maintenance',
                'condition' => 'fair',
                'description' => '5-piece acoustic drum kit with cymbals',
                'image' => 'fas fa-drum',
                'serial_number' => 'PRL-004',
                'purchase_date' => Carbon::parse('2023-04-05'),
                'last_maintenance' => Carbon::parse('2024-01-20'),
            ],
            [
                'name' => 'Trumpet - Bach TR300H2',
                'category' => 'brass',
                'type' => 'Trumpet',
                'brand' => 'Bach',
                'model' => 'TR300H2',
                'daily_rate' => 1800.00,
                'status' => 'available',
                'condition' => 'good',
                'description' => 'Student trumpet with gold brass bell',
                'image' => 'fas fa-trumpet',
                'serial_number' => 'BCH-005',
                'purchase_date' => Carbon::parse('2023-05-12'),
                'last_maintenance' => Carbon::parse('2024-01-15'),
            ],
            [
                'name' => 'Violin - Mendini MV300',
                'category' => 'strings',
                'type' => 'Violin',
                'brand' => 'Mendini',
                'model' => 'MV300',
                'daily_rate' => 1200.00,
                'status' => 'available',
                'condition' => 'excellent',
                'description' => '4/4 size violin with case and bow',
                'image' => 'fas fa-violin',
                'serial_number' => 'MEN-006',
                'purchase_date' => Carbon::parse('2023-06-18'),
                'last_maintenance' => Carbon::parse('2024-01-12'),
            ],
            [
                'name' => 'Bass Guitar - Ibanez SR300E',
                'category' => 'guitars',
                'type' => 'Bass Guitar',
                'brand' => 'Ibanez',
                'model' => 'SR300E',
                'daily_rate' => 1800.00,
                'status' => 'available',
                'condition' => 'good',
                'description' => '4-string bass guitar with active electronics',
                'image' => 'fas fa-guitar',
                'serial_number' => 'IBZ-007',
                'purchase_date' => Carbon::parse('2023-07-22'),
                'last_maintenance' => Carbon::parse('2024-01-14'),
            ],
            [
                'name' => 'Synthesizer - Roland JUNO-DS61',
                'category' => 'keyboards',
                'type' => 'Synthesizer',
                'brand' => 'Roland',
                'model' => 'JUNO-DS61',
                'daily_rate' => 2200.00,
                'status' => 'available',
                'condition' => 'excellent',
                'description' => '61-key synthesizer with over 1000 sounds',
                'image' => 'fas fa-piano',
                'serial_number' => 'ROL-008',
                'purchase_date' => Carbon::parse('2023-08-15'),
                'last_maintenance' => Carbon::parse('2024-01-11'),
            ],
        ];

        foreach ($props as $propData) {
            Prop::create($propData);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+234 801 234 5678',
                'address' => '123 Music Street, Lagos',
                'id_number' => 'ID123456789',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+234 802 345 6789',
                'address' => '456 Harmony Ave, Abuja',
                'id_number' => 'ID987654321',
                'status' => 'active',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@email.com',
                'phone' => '+234 803 456 7890',
                'address' => '789 Rhythm Road, Ibadan',
                'id_number' => 'ID456789123',
                'status' => 'active',
            ],
            [
                'name' => 'Emily Brown',
                'email' => 'emily.brown@email.com',
                'phone' => '+234 804 567 8901',
                'address' => '321 Melody Lane, Port Harcourt',
                'id_number' => 'ID789123456',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customerData) {
            RentalCustomer::create($customerData);
        }

        // Create Sample Rentals
        $guitar = Prop::where('serial_number', 'FEN-002')->first();
        $violin = Prop::where('serial_number', 'MEN-006')->first();
        $customer1 = RentalCustomer::where('email', 'john.smith@email.com')->first();
        $customer2 = RentalCustomer::where('email', 'sarah.johnson@email.com')->first();

        if ($guitar && $customer1) {
            $startDate = Carbon::now()->subDays(2);
            $endDate = Carbon::now()->addDays(5);
            $days = $startDate->diffInDays($endDate);
            
            $rental = PropRental::create([
                'prop_id' => $guitar->id,
                'rental_customer_id' => $customer1->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_rate' => $guitar->daily_rate,
                'total_amount' => $days * $guitar->daily_rate,
                'security_deposit' => 5000,
                'amount_paid' => $days * $guitar->daily_rate,
                'status' => 'active',
                'notes' => 'Regular customer, handles equipment well',
                'agreement_signed' => true,
            ]);

            $guitar->update(['status' => 'rented']);
            $customer1->incrementRentalStats($rental->total_amount);
        }

        if ($violin && $customer2) {
            $startDate = Carbon::now()->subDays(1);
            $endDate = Carbon::now()->addDays(2);
            $days = $startDate->diffInDays($endDate);
            
            $rental = PropRental::create([
                'prop_id' => $violin->id,
                'rental_customer_id' => $customer2->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_rate' => $violin->daily_rate,
                'total_amount' => $days * $violin->daily_rate,
                'security_deposit' => 2000,
                'amount_paid' => $days * $violin->daily_rate,
                'status' => 'active',
                'notes' => 'Student rental for music lessons',
                'agreement_signed' => true,
            ]);

            $violin->update(['status' => 'rented']);
            $customer2->incrementRentalStats($rental->total_amount);
        }

        $this->command->info('Prop rental data seeded successfully!');
    }
}