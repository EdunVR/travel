<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\TravelPackage;
use App\Models\HppCalculation;
use App\Models\Member;
use App\Models\Keberangkatan;
use App\Models\JamaahBooking;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class UATDataSeeder extends Seeder
{
    /**
     * Run the database seeds for User Acceptance Testing.
     * Creates realistic sample data for comprehensive testing.
     */
    public function run(): void
    {
        $this->command->info('Starting UAT Data Seeding...');
        
        // Get default outlet (assuming outlet ID 1 exists)
        $outletId = 1;
        
        // 1. Create Test Users for Different Teams
        $this->createTestUsers();
        
        // 2. Create Master Data - Flights
        $this->createFlights($outletId);
        
        // 3. Create Master Data - Hotels
        $this->createHotels($outletId);
        
        // 4. Create Travel Packages with HPP
        $packages = $this->createTravelPackages($outletId);
        
        // 5. Create Jamaah (Pilgrims)
        $jamaah = $this->createJamaah($outletId);
        
        // 6. Create Keberangkatan (Departures)
        $departures = $this->createKeberangkatan($outletId, $packages);
        
        // 7. Create Jamaah Bookings
        $this->createJamaahBookings($outletId, $packages, $jamaah, $departures);
        
        $this->command->info('UAT Data Seeding Completed Successfully!');
        $this->command->info('Test Users Created:');
        $this->command->info('  - admin@test.com / password (Admin)');
        $this->command->info('  - finance@test.com / password (Finance Team)');
        $this->command->info('  - admin_team@test.com / password (Administration Team)');
        $this->command->info('  - cs@test.com / password (Customer Service Team)');
        $this->command->info('  - media@test.com / password (Media Team)');
        $this->command->info('  - logistics@test.com / password (Logistics Team)');
    }
    
    private function createTestUsers()
    {
        $this->command->info('Creating test users...');
        
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Finance Officer',
                'email' => 'finance@test.com',
                'password' => Hash::make('password'),
                'role' => 'finance'
            ],
            [
                'name' => 'Administration Officer',
                'email' => 'admin_team@test.com',
                'password' => Hash::make('password'),
                'role' => 'administration'
            ],
            [
                'name' => 'Customer Service Rep',
                'email' => 'cs@test.com',
                'password' => Hash::make('password'),
                'role' => 'customer_service'
            ],
            [
                'name' => 'Media Coordinator',
                'email' => 'media@test.com',
                'password' => Hash::make('password'),
                'role' => 'media'
            ],
            [
                'name' => 'Logistics Coordinator',
                'email' => 'logistics@test.com',
                'password' => Hash::make('password'),
                'role' => 'logistics'
            ],
        ];
        
        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
    
    private function createFlights($outletId)
    {
        $this->command->info('Creating flight master data...');
        
        $flights = [
            [
                'airline_name' => 'Garuda Indonesia',
                'flight_number' => 'GA-9001',
                'departure_airport' => 'CGK (Jakarta)',
                'arrival_airport' => 'JED (Jeddah)',
                'departure_time' => '08:00:00',
                'arrival_time' => '14:30:00',
                'capacity' => 250,
                'aircraft_type' => 'Boeing 777-300ER',
                'id_outlet' => $outletId
            ],
            [
                'airline_name' => 'Saudi Arabian Airlines',
                'flight_number' => 'SV-815',
                'departure_airport' => 'CGK (Jakarta)',
                'arrival_airport' => 'JED (Jeddah)',
                'departure_time' => '22:00:00',
                'arrival_time' => '04:30:00',
                'capacity' => 300,
                'aircraft_type' => 'Airbus A330-300',
                'id_outlet' => $outletId
            ],
            [
                'airline_name' => 'Emirates',
                'flight_number' => 'EK-357',
                'departure_airport' => 'CGK (Jakarta)',
                'arrival_airport' => 'JED (Jeddah)',
                'departure_time' => '01:30:00',
                'arrival_time' => '07:45:00',
                'capacity' => 350,
                'aircraft_type' => 'Boeing 777-300ER',
                'id_outlet' => $outletId
            ],
            [
                'airline_name' => 'Lion Air',
                'flight_number' => 'JT-1001',
                'departure_airport' => 'CGK (Jakarta)',
                'arrival_airport' => 'JED (Jeddah)',
                'departure_time' => '15:00:00',
                'arrival_time' => '21:30:00',
                'capacity' => 200,
                'aircraft_type' => 'Boeing 737-900ER',
                'id_outlet' => $outletId
            ],
        ];
        
        foreach ($flights as $flightData) {
            Flight::firstOrCreate(
                ['flight_number' => $flightData['flight_number']],
                $flightData
            );
        }
    }
    
    private function createHotels($outletId)
    {
        $this->command->info('Creating hotel master data...');
        
        $hotels = [
            [
                'hotel_name' => 'Hilton Makkah Convention Hotel',
                'location' => 'Makkah',
                'city' => 'Makkah',
                'country' => 'Saudi Arabia',
                'star_rating' => 5,
                'total_rooms' => 500,
                'contact_person' => 'Ahmed Al-Rashid',
                'phone' => '+966-12-5551234',
                'email' => 'reservations@hiltonmakkah.com',
                'address' => 'King Abdul Aziz Road, Makkah',
                'id_outlet' => $outletId,
                'room_types' => [
                    ['room_type_name' => 'Standard Double', 'capacity' => 2, 'total_rooms' => 200, 'price_per_night' => 150],
                    ['room_type_name' => 'Triple Room', 'capacity' => 3, 'total_rooms' => 150, 'price_per_night' => 200],
                    ['room_type_name' => 'Quad Room', 'capacity' => 4, 'total_rooms' => 150, 'price_per_night' => 250],
                ]
            ],
            [
                'hotel_name' => 'Swissotel Makkah',
                'location' => 'Makkah',
                'city' => 'Makkah',
                'country' => 'Saudi Arabia',
                'star_rating' => 5,
                'total_rooms' => 400,
                'contact_person' => 'Mohammed Hassan',
                'phone' => '+966-12-5552345',
                'email' => 'info@swissotelmakkah.com',
                'address' => 'Ibrahim Al Khalil Street, Makkah',
                'id_outlet' => $outletId,
                'room_types' => [
                    ['room_type_name' => 'Standard Double', 'capacity' => 2, 'total_rooms' => 150, 'price_per_night' => 180],
                    ['room_type_name' => 'Triple Room', 'capacity' => 3, 'total_rooms' => 150, 'price_per_night' => 230],
                    ['room_type_name' => 'Quad Room', 'capacity' => 4, 'total_rooms' => 100, 'price_per_night' => 280],
                ]
            ],
            [
                'hotel_name' => 'Dar Al Eiman Royal Hotel',
                'location' => 'Madinah',
                'city' => 'Madinah',
                'country' => 'Saudi Arabia',
                'star_rating' => 4,
                'total_rooms' => 300,
                'contact_person' => 'Abdullah Ibrahim',
                'phone' => '+966-14-8881234',
                'email' => 'reservations@daraleiman.com',
                'address' => 'King Fahd Road, Madinah',
                'id_outlet' => $outletId,
                'room_types' => [
                    ['room_type_name' => 'Standard Double', 'capacity' => 2, 'total_rooms' => 120, 'price_per_night' => 120],
                    ['room_type_name' => 'Triple Room', 'capacity' => 3, 'total_rooms' => 100, 'price_per_night' => 160],
                    ['room_type_name' => 'Quad Room', 'capacity' => 4, 'total_rooms' => 80, 'price_per_night' => 200],
                ]
            ],
        ];
        
        foreach ($hotels as $hotelData) {
            $roomTypes = $hotelData['room_types'];
            unset($hotelData['room_types']);
            
            $hotel = Hotel::firstOrCreate(
                ['hotel_name' => $hotelData['hotel_name']],
                $hotelData
            );
            
            foreach ($roomTypes as $roomTypeData) {
                $roomTypeData['id_hotel'] = $hotel->id;
                HotelRoomType::firstOrCreate(
                    ['id_hotel' => $hotel->id, 'room_type_name' => $roomTypeData['room_type_name']],
                    $roomTypeData
                );
            }
        }
    }
    
    private function createTravelPackages($outletId)
    {
        $this->command->info('Creating travel packages with HPP...');
        
        $packages = [];
        
        // Package 1: Umrah 9 Days
        $package1 = TravelPackage::firstOrCreate(
            ['package_code' => 'UMR-001-2026'],
            [
                'package_code' => 'UMR-001-2026',
                'package_name' => 'Umrah Ekonomi 9 Hari',
                'package_type' => 'umrah',
                'description' => 'Paket Umrah ekonomis 9 hari dengan fasilitas lengkap',
                'duration_days' => 9,
                'departure_date' => '2026-03-15',
                'return_date' => '2026-03-23',
                'capacity' => 40,
                'price' => 25000000,
                'status' => 'active',
                'current_workflow_stage' => 'product_analysis',
                'id_outlet' => $outletId,
                'inclusions' => json_encode([
                    'Tiket pesawat PP Jakarta-Jeddah',
                    'Hotel bintang 4 dekat Masjidil Haram',
                    'Transportasi AC selama di Arab Saudi',
                    'Makan 3x sehari',
                    'Visa Umrah',
                    'Perlengkapan Umrah',
                    'Pembimbing berpengalaman',
                    'Asuransi perjalanan'
                ]),
                'popularity_score' => 85
            ]
        );
        
        HppCalculation::firstOrCreate(
            ['id_travel_package' => $package1->id],
            [
                'id_travel_package' => $package1->id,
                'flight_cost' => 8000000,
                'hotel_cost' => 6000000,
                'transportation_cost' => 1500000,
                'meal_cost' => 2000000,
                'visa_cost' => 1200000,
                'guide_cost' => 800000,
                'insurance_cost' => 500000,
                'operational_overhead' => 1000000,
                'contingency' => 500000,
                'total_hpp' => 21500000,
                'is_locked' => false
            ]
        );
        
        $packages[] = $package1;
        
        // Package 2: Hajj 14 Days
        $package2 = TravelPackage::firstOrCreate(
            ['package_code' => 'HAJ-001-2026'],
            [
                'package_code' => 'HAJ-001-2026',
                'package_name' => 'Hajj Reguler 14 Hari',
                'package_type' => 'hajj',
                'description' => 'Paket Hajj reguler 14 hari dengan bimbingan intensif',
                'duration_days' => 14,
                'departure_date' => '2026-06-10',
                'return_date' => '2026-06-23',
                'capacity' => 100,
                'price' => 55000000,
                'status' => 'active',
                'current_workflow_stage' => 'product_analysis',
                'id_outlet' => $outletId,
                'inclusions' => json_encode([
                    'Tiket pesawat PP Jakarta-Jeddah',
                    'Hotel bintang 5 dekat Masjidil Haram dan Nabawi',
                    'Transportasi AC selama di Arab Saudi',
                    'Makan 3x sehari',
                    'Visa Hajj',
                    'Perlengkapan Hajj lengkap',
                    'Pembimbing berpengalaman dan mutawif',
                    'Asuransi perjalanan',
                    'Manasik Hajj sebelum keberangkatan'
                ]),
                'popularity_score' => 95
            ]
        );
        
        HppCalculation::firstOrCreate(
            ['id_travel_package' => $package2->id],
            [
                'id_travel_package' => $package2->id,
                'flight_cost' => 12000000,
                'hotel_cost' => 18000000,
                'transportation_cost' => 3000000,
                'meal_cost' => 4000000,
                'visa_cost' => 2500000,
                'guide_cost' => 2000000,
                'insurance_cost' => 800000,
                'operational_overhead' => 2500000,
                'contingency' => 1200000,
                'total_hpp' => 46000000,
                'is_locked' => false
            ]
        );
        
        $packages[] = $package2;
        
        // Package 3: Umrah 12 Days (Draft)
        $package3 = TravelPackage::firstOrCreate(
            ['package_code' => 'UMR-002-2026'],
            [
                'package_code' => 'UMR-002-2026',
                'package_name' => 'Umrah Plus 12 Hari',
                'package_type' => 'umrah',
                'description' => 'Paket Umrah 12 hari dengan kunjungan wisata religi',
                'duration_days' => 12,
                'departure_date' => '2026-04-20',
                'return_date' => '2026-05-01',
                'capacity' => 30,
                'price' => 32000000,
                'status' => 'draft',
                'current_workflow_stage' => 'product_analysis',
                'id_outlet' => $outletId,
                'inclusions' => json_encode([
                    'Tiket pesawat PP Jakarta-Jeddah',
                    'Hotel bintang 5 dekat Masjidil Haram',
                    'Transportasi AC selama di Arab Saudi',
                    'Makan 3x sehari',
                    'Visa Umrah',
                    'Perlengkapan Umrah premium',
                    'Pembimbing berpengalaman',
                    'Asuransi perjalanan',
                    'City tour Makkah dan Madinah',
                    'Kunjungan tempat bersejarah'
                ]),
                'popularity_score' => 70
            ]
        );
        
        HppCalculation::firstOrCreate(
            ['id_travel_package' => $package3->id],
            [
                'id_travel_package' => $package3->id,
                'flight_cost' => 9000000,
                'hotel_cost' => 10000000,
                'transportation_cost' => 2000000,
                'meal_cost' => 2500000,
                'visa_cost' => 1200000,
                'guide_cost' => 1200000,
                'insurance_cost' => 600000,
                'operational_overhead' => 1500000,
                'contingency' => 800000,
                'total_hpp' => 28800000,
                'is_locked' => false
            ]
        );
        
        $packages[] = $package3;
        
        return $packages;
    }
    
    private function createJamaah($outletId)
    {
        $this->command->info('Creating jamaah (pilgrims)...');
        
        $jamaah = [];
        
        // Create 50 jamaah with varied profiles
        $firstNames = ['Ahmad', 'Fatimah', 'Muhammad', 'Aisyah', 'Abdullah', 'Khadijah', 'Ibrahim', 'Maryam', 'Umar', 'Zainab'];
        $lastNames = ['Rahman', 'Hidayat', 'Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Sari', 'Putri', 'Hakim', 'Nasution'];
        
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName . ' ' . $i;
            
            $gender = ($i % 2 == 0) ? 'female' : 'male';
            $age = rand(25, 65);
            $birthDate = now()->subYears($age)->format('Y-m-d');
            
            $member = Member::firstOrCreate(
                ['email' => 'jamaah' . $i . '@test.com'],
                [
                    'nama_member' => $fullName,
                    'email' => 'jamaah' . $i . '@test.com',
                    'no_telp' => '08' . rand(1000000000, 9999999999),
                    'alamat' => 'Jl. Test No. ' . $i . ', Jakarta',
                    'tanggal_lahir' => $birthDate,
                    'jenis_kelamin' => $gender,
                    'id_outlet' => $outletId,
                    'is_jamaah' => true,
                    'jamaah_type' => ($i <= 35) ? 'umrah' : 'hajj',
                    'ktp_nik' => '3175' . str_pad($i, 12, '0', STR_PAD_LEFT),
                    'ktp_nama' => $fullName,
                    'ktp_tempat_lahir' => 'Jakarta',
                    'ktp_tanggal_lahir' => $birthDate,
                    'ktp_alamat' => 'Jl. Test No. ' . $i . ', Jakarta',
                    'passport_number' => 'A' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'passport_nama' => $fullName,
                    'passport_tanggal_lahir' => $birthDate,
                    'passport_tempat_lahir' => 'Jakarta',
                    'passport_tanggal_terbit' => now()->subYears(2)->format('Y-m-d'),
                    'passport_tanggal_habis_berlaku' => now()->addYears(8)->format('Y-m-d'),
                    'health_conditions' => ($i % 5 == 0) ? 'Diabetes terkontrol' : 'Sehat',
                    'emergency_contact_name' => 'Emergency Contact ' . $i,
                    'emergency_contact_phone' => '08' . rand(1000000000, 9999999999),
                    'room_preference' => ['double', 'triple', 'quad'][rand(0, 2)],
                ]
            );
            
            // Add mahram for female jamaah under 45
            if ($gender == 'female' && $age < 45) {
                $member->update([
                    'mahram_name' => 'Mahram ' . $fullName,
                    'mahram_relationship' => ['husband', 'father', 'brother'][rand(0, 2)],
                    'mahram_phone' => '08' . rand(1000000000, 9999999999),
                ]);
            }
            
            $jamaah[] = $member;
        }
        
        return $jamaah;
    }
    
    private function createKeberangkatan($outletId, $packages)
    {
        $this->command->info('Creating keberangkatan (departures)...');
        
        $departures = [];
        
        // Departure 1 for Package 1 (Umrah 9 Days)
        $departure1 = Keberangkatan::firstOrCreate(
            ['keberangkatan_code' => 'KBR-001-2026'],
            [
                'keberangkatan_code' => 'KBR-001-2026',
                'keberangkatan_name' => 'Keberangkatan Umrah Maret 2026 - Batch 1',
                'id_travel_package' => $packages[0]->id,
                'departure_date' => '2026-03-15',
                'return_date' => '2026-03-23',
                'total_jamaah' => 35,
                'status' => 'confirmed',
                'id_outlet' => $outletId
            ]
        );
        $departures[] = $departure1;
        
        // Departure 2 for Package 2 (Hajj 14 Days)
        $departure2 = Keberangkatan::firstOrCreate(
            ['keberangkatan_code' => 'KBR-002-2026'],
            [
                'keberangkatan_code' => 'KBR-002-2026',
                'keberangkatan_name' => 'Keberangkatan Hajj Juni 2026 - Batch 1',
                'id_travel_package' => $packages[1]->id,
                'departure_date' => '2026-06-10',
                'return_date' => '2026-06-23',
                'total_jamaah' => 85,
                'status' => 'planning',
                'id_outlet' => $outletId
            ]
        );
        $departures[] = $departure2;
        
        return $departures;
    }
    
    private function createJamaahBookings($outletId, $packages, $jamaah, $departures)
    {
        $this->command->info('Creating jamaah bookings...');
        
        // Book first 35 jamaah for Package 1 / Departure 1
        for ($i = 0; $i < 35; $i++) {
            JamaahBooking::firstOrCreate(
                ['booking_code' => 'BKG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT)],
                [
                    'booking_code' => 'BKG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'id_travel_package' => $packages[0]->id,
                    'id_member' => $jamaah[$i]->id,
                    'id_keberangkatan' => $departures[0]->id,
                    'booking_date' => now()->subDays(rand(10, 30)),
                    'status' => 'confirmed',
                    'total_price' => 25000000,
                    'payment_status' => ($i < 20) ? 'paid' : (($i < 30) ? 'partial' : 'unpaid'),
                    'paid_amount' => ($i < 20) ? 25000000 : (($i < 30) ? 15000000 : 0),
                    'remaining_amount' => ($i < 20) ? 0 : (($i < 30) ? 10000000 : 25000000),
                    'id_outlet' => $outletId
                ]
            );
        }
        
        // Book next 10 jamaah for Package 2 / Departure 2 (partial bookings)
        for ($i = 35; $i < 45; $i++) {
            JamaahBooking::firstOrCreate(
                ['booking_code' => 'BKG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT)],
                [
                    'booking_code' => 'BKG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'id_travel_package' => $packages[1]->id,
                    'id_member' => $jamaah[$i]->id,
                    'id_keberangkatan' => $departures[1]->id,
                    'booking_date' => now()->subDays(rand(5, 15)),
                    'status' => 'pending',
                    'total_price' => 55000000,
                    'payment_status' => 'unpaid',
                    'paid_amount' => 0,
                    'remaining_amount' => 55000000,
                    'id_outlet' => $outletId
                ]
            );
        }
    }
}
