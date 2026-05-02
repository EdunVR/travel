<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirlineAirportSeeder extends Seeder
{
    public function run(): void
    {
        // Airlines
        $airlines = [
            ['name' => 'Saudi Airlines', 'iata_code' => 'SV', 'country' => 'Saudi Arabia'],
            ['name' => 'Garuda Airlines', 'iata_code' => 'GA', 'country' => 'Indonesia'],
            ['name' => 'Lion Air', 'iata_code' => 'JT', 'country' => 'Indonesia'],
            ['name' => 'Etihad Airways', 'iata_code' => 'EY', 'country' => 'UAE'],
            ['name' => 'Fly Emirates', 'iata_code' => 'EK', 'country' => 'UAE'],
            ['name' => 'Qatar Airways', 'iata_code' => 'QR', 'country' => 'Qatar'],
            ['name' => 'Turkish Airlines', 'iata_code' => 'TK', 'country' => 'Turkey'],
            ['name' => 'Ajet Airlines', 'iata_code' => 'VF', 'country' => 'Turkey'],
        ];

        foreach ($airlines as $airline) {
            DB::table('airlines')->updateOrInsert(
                ['name' => $airline['name']],
                array_merge($airline, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // Airports
        $airports = [
            ['iata_code' => 'CGK', 'name' => 'Soekarno-Hatta International Airport', 'city' => 'Jakarta', 'country' => 'Indonesia'],
            ['iata_code' => 'JED', 'name' => 'King Abdulaziz International Airport', 'city' => 'Jeddah', 'country' => 'Saudi Arabia'],
            ['iata_code' => 'MED', 'name' => 'Prince Mohammad bin Abdulaziz Airport', 'city' => 'Madinah', 'country' => 'Saudi Arabia'],
            ['iata_code' => 'AUH', 'name' => 'Abu Dhabi International Airport', 'city' => 'Abu Dhabi', 'country' => 'UAE'],
            ['iata_code' => 'DXB', 'name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'UAE'],
            ['iata_code' => 'DIA', 'name' => 'Hamad International Airport', 'city' => 'Doha', 'country' => 'Qatar'],
            ['iata_code' => 'IST', 'name' => 'Istanbul Airport', 'city' => 'Istanbul', 'country' => 'Turkey'],
            ['iata_code' => 'SAW', 'name' => 'Istanbul Sabiha Gokcen Airport', 'city' => 'Istanbul', 'country' => 'Turkey'],
            ['iata_code' => 'ESB', 'name' => 'Esenboga International Airport', 'city' => 'Ankara', 'country' => 'Turkey'],
        ];

        foreach ($airports as $airport) {
            DB::table('airports')->updateOrInsert(
                ['iata_code' => $airport['iata_code']],
                array_merge($airport, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
