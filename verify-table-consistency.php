<?php

/**
 * Script untuk memverifikasi konsistensi kolom antara Controller, Model, dan Database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== VERIFIKASI KONSISTENSI TABEL ===\n\n";

$tables = [
    'flights' => [
        'model' => 'App\Models\Flight',
        'controller_fields' => ['airline_name', 'flight_number', 'departure_airport', 'arrival_airport', 
                                'departure_time', 'arrival_time', 'capacity', 'aircraft_type', 
                                'price_per_person', 'seller_name', 'seller_phone', 'id_outlet']
    ],
    'hotels' => [
        'model' => 'App\Models\Hotel',
        'controller_fields' => ['hotel_name', 'location', 'city', 'country', 'star_rating', 
                                'total_rooms', 'contact_person', 'phone', 'email', 'address', 'id_outlet']
    ],
    'saudi_transports' => [
        'model' => 'App\Models\SaudiTransport',
        'controller_fields' => ['transport_name', 'transport_type', 'route_from', 'route_to', 
                                'operator', 'price_per_person', 'seller_name', 'seller_phone', 
                                'notes', 'id_outlet']
    ],
    'airlines' => [
        'model' => 'App\Models\Airline',
        'controller_fields' => ['name', 'iata_code', 'country']
    ],
    'airports' => [
        'model' => 'App\Models\Airport',
        'controller_fields' => ['iata_code', 'name', 'city', 'country']
    ]
];

foreach ($tables as $tableName => $config) {
    echo "--- Tabel: $tableName ---\n";
    
    // Cek kolom di database
    $dbColumns = Schema::getColumnListing($tableName);
    echo "✓ Kolom di database: " . count($dbColumns) . " kolom\n";
    
    // Cek fillable di model
    $modelClass = $config['model'];
    if (class_exists($modelClass)) {
        $model = new $modelClass;
        $fillable = $model->getFillable();
        echo "✓ Fillable di model: " . count($fillable) . " kolom\n";
        
        // Cek apakah semua controller fields ada di fillable
        $missingInFillable = array_diff($config['controller_fields'], $fillable);
        if (!empty($missingInFillable)) {
            echo "⚠ PERINGATAN: Kolom di controller tapi tidak di fillable: " . implode(', ', $missingInFillable) . "\n";
        }
        
        // Cek apakah semua fillable ada di database
        $missingInDb = array_diff($fillable, $dbColumns);
        if (!empty($missingInDb)) {
            echo "❌ ERROR: Kolom di fillable tapi tidak di database: " . implode(', ', $missingInDb) . "\n";
        } else {
            echo "✓ Semua kolom fillable ada di database\n";
        }
        
        // Cek apakah semua controller fields ada di database
        $missingControllerInDb = array_diff($config['controller_fields'], $dbColumns);
        if (!empty($missingControllerInDb)) {
            echo "❌ ERROR: Kolom di controller tapi tidak di database: " . implode(', ', $missingControllerInDb) . "\n";
        } else {
            echo "✓ Semua kolom controller ada di database\n";
        }
    } else {
        echo "❌ Model tidak ditemukan: $modelClass\n";
    }
    
    echo "\n";
}

echo "=== VERIFIKASI SELESAI ===\n";
