<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\FlightBooking;
use App\Models\Keberangkatan;
use App\Models\TravelPackage;
use App\Models\HppCalculation;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class FlightBookingPropertiesTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create only the tables we need for this test
        $this->createRequiredTables();
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Disable foreign key checks before cleanup
        Schema::disableForeignKeyConstraints();
        
        // Clean up tables in reverse order of dependencies
        Schema::dropIfExists('jamaah_bookings');
        Schema::dropIfExists('flight_bookings');
        Schema::dropIfExists('keberangkatan');
        Schema::dropIfExists('hpp_calculations');
        Schema::dropIfExists('travel_packages');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('outlets');
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
        
        parent::tearDown();
    }

    /**
     * Create the minimal tables required for flight booking testing.
     */
    protected function createRequiredTables(): void
    {
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();
        
        // Drop tables if they exist
        Schema::dropIfExists('flight_bookings');
        Schema::dropIfExists('keberangkatan');
        Schema::dropIfExists('hpp_calculations');
        Schema::dropIfExists('travel_packages');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('outlets');

        // Create outlets table (required for foreign key)
        Schema::create('outlets', function (Blueprint $table) {
            $table->id('id_outlet');
            $table->string('nama_outlet');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        // Create flights table
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('airline_name');
            $table->string('flight_number');
            $table->string('departure_airport');
            $table->string('arrival_airport');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('capacity')->unsigned();
            $table->string('aircraft_type')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });

        // Create travel_packages table
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_code')->unique();
            $table->string('package_name');
            $table->enum('package_type', ['hajj', 'umrah']);
            $table->text('description')->nullable();
            $table->integer('duration_days');
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('capacity')->unsigned();
            $table->decimal('price', 15, 2);
            $table->decimal('hpp', 15, 2)->nullable();
            $table->decimal('profit_margin', 5, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'full', 'completed', 'cancelled'])->default('draft');
            $table->string('current_workflow_stage')->default('product_analysis');
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });

        // Create hpp_calculations table
        Schema::create('hpp_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->decimal('flight_cost', 15, 2)->default(0);
            $table->decimal('hotel_cost', 15, 2)->default(0);
            $table->decimal('transportation_cost', 15, 2)->default(0);
            $table->decimal('meal_cost', 15, 2)->default(0);
            $table->decimal('visa_cost', 15, 2)->default(0);
            $table->decimal('guide_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('operational_overhead', 15, 2)->default(0);
            $table->decimal('contingency', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0);
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamps();
            
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
        });

        // Create keberangkatan table
        Schema::create('keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->string('keberangkatan_code')->unique();
            $table->string('keberangkatan_name');
            $table->unsignedBigInteger('id_travel_package');
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('total_jamaah')->default(0);
            $table->enum('status', ['planning', 'confirmed', 'departed', 'completed'])->default('planning');
            $table->unsignedBigInteger('id_rab')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });

        // Create flight_bookings table
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_flight');
            $table->unsignedBigInteger('id_keberangkatan');
            $table->integer('seat_count')->unsigned();
            $table->enum('status', ['pending', 'confirmed', 'ticketed', 'cancelled'])->default('pending');
            $table->string('booking_reference')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->string('ticket_document_path')->nullable();
            $table->date('booking_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('id_flight')->references('id')->on('flights')->onDelete('cascade');
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
        });
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Feature: hajj-umrah-travel-system, Property 23: Flight Tickets Stage Completion
     * Validates: Requirements 7.7
     * 
     * Property: For any keberangkatan, if all jamaah have flight tickets with 
     * status = 'confirmed', the Flight_Tickets workflow stage should be marked 
     * as complete.
     * 
     * This test runs 100 iterations with various scenarios to verify that the
     * flight tickets stage completion logic works correctly across all cases:
     * - Keberangkatan with no jamaah (should not be complete)
     * - Keberangkatan with partial confirmed tickets (should not be complete)
     * - Keberangkatan with all confirmed tickets (should be complete)
     * - Keberangkatan with excess confirmed tickets (should be complete)
     * - Keberangkatan with cancelled bookings (should not count)
     * 
     * Note: The hasAllFlightTicketsConfirmed() method compares confirmed flight
     * bookings against actual jamaah bookings (getConfirmedJamaahCount()), not
     * the total_jamaah field. This test creates jamaah bookings to properly
     * test the property.
     */
    public function test_flight_tickets_stage_completion(): void
    {
        // First create jamaah_bookings table for testing
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('jamaah_bookings');
        
        Schema::create('jamaah_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->unsignedBigInteger('id_travel_package');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_keberangkatan')->nullable();
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'paid', 'departed', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 15, 2);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
        
        Schema::enableForeignKeyConstraints();
        
        // Run 100 iterations with random scenarios
        for ($i = 0; $i < 100; $i++) {
            // Create a test outlet
            $outlet = Outlet::create([
                'nama_outlet' => fake()->company(),
                'alamat' => fake()->address(),
                'telepon' => fake()->phoneNumber(),
            ]);
            
            // Create a travel package
            $package = TravelPackage::create([
                'package_code' => 'PKG-' . fake()->unique()->numerify('####'),
                'package_name' => fake()->words(3, true) . ' Package',
                'package_type' => fake()->randomElement(['hajj', 'umrah']),
                'description' => fake()->sentence(),
                'duration_days' => fake()->numberBetween(7, 30),
                'departure_date' => fake()->dateTimeBetween('now', '+6 months'),
                'return_date' => fake()->dateTimeBetween('+7 days', '+7 months'),
                'capacity' => fake()->numberBetween(20, 100),
                'price' => fake()->numberBetween(20000000, 50000000),
                'hpp' => fake()->numberBetween(15000000, 40000000),
                'status' => 'active',
                'current_workflow_stage' => 'flight_tickets',
                'id_outlet' => $outlet->id_outlet,
            ]);
            
            // Create HPP calculation
            HppCalculation::create([
                'id_travel_package' => $package->id,
                'flight_cost' => fake()->numberBetween(5000000, 10000000),
                'hotel_cost' => fake()->numberBetween(3000000, 8000000),
                'transportation_cost' => fake()->numberBetween(500000, 2000000),
                'meal_cost' => fake()->numberBetween(1000000, 3000000),
                'visa_cost' => fake()->numberBetween(2000000, 4000000),
                'guide_cost' => fake()->numberBetween(500000, 1500000),
                'insurance_cost' => fake()->numberBetween(300000, 1000000),
                'operational_overhead' => fake()->numberBetween(1000000, 3000000),
                'contingency' => fake()->numberBetween(500000, 2000000),
                'total_hpp' => fake()->numberBetween(15000000, 40000000),
            ]);
            
            // Create a keberangkatan
            $totalJamaah = fake()->numberBetween(10, 50);
            $keberangkatan = Keberangkatan::create([
                'keberangkatan_code' => 'KBR-' . fake()->unique()->numerify('####'),
                'keberangkatan_name' => fake()->words(2, true) . ' Batch',
                'id_travel_package' => $package->id,
                'departure_date' => $package->departure_date,
                'return_date' => $package->return_date,
                'total_jamaah' => $totalJamaah,
                'status' => 'confirmed',
                'id_outlet' => $outlet->id_outlet,
            ]);
            
            // Create jamaah bookings for this keberangkatan
            // We'll create bookings for the total_jamaah count
            for ($j = 0; $j < $totalJamaah; $j++) {
                \DB::table('jamaah_bookings')->insert([
                    'booking_code' => 'JMH-' . fake()->unique()->numerify('####'),
                    'id_travel_package' => $package->id,
                    'id_member' => fake()->numberBetween(1, 1000), // Dummy member ID
                    'id_keberangkatan' => $keberangkatan->id,
                    'booking_date' => now(),
                    'status' => 'confirmed', // Confirmed jamaah booking
                    'total_price' => $package->price,
                    'payment_status' => 'paid',
                    'paid_amount' => $package->price,
                    'remaining_amount' => 0,
                    'id_outlet' => $outlet->id_outlet,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Create a flight
            $flight = Flight::create([
                'airline_name' => fake()->randomElement(['Garuda Indonesia', 'Saudi Arabian Airlines', 'Emirates']),
                'flight_number' => fake()->bothify('??###'),
                'departure_airport' => 'CGK',
                'arrival_airport' => 'JED',
                'departure_time' => fake()->dateTimeBetween('now', '+6 months'),
                'arrival_time' => fake()->dateTimeBetween('+1 day', '+6 months'),
                'capacity' => fake()->numberBetween(100, 500),
                'aircraft_type' => 'Boeing 777',
                'id_outlet' => $outlet->id_outlet,
            ]);
            
            // Randomly choose a scenario
            $scenario = fake()->randomElement([
                'no_bookings',           // No flight bookings at all
                'partial_confirmed',     // Some confirmed, but not enough
                'all_confirmed',         // Exactly enough confirmed tickets
                'excess_confirmed',      // More confirmed tickets than jamaah
                'with_cancelled',        // Mix of confirmed and cancelled
                'pending_only',          // Only pending bookings
            ]);
            
            $confirmedSeats = 0;
            
            switch ($scenario) {
                case 'no_bookings':
                    // No bookings created
                    break;
                    
                case 'partial_confirmed':
                    // Create confirmed bookings for less than total jamaah
                    $confirmedSeats = fake()->numberBetween(1, $totalJamaah - 1);
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => $confirmedSeats,
                        'status' => 'confirmed',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'confirmation_code' => fake()->bothify('CF####??'),
                        'booking_date' => now(),
                        'confirmed_at' => now(),
                    ]);
                    break;
                    
                case 'all_confirmed':
                    // Create confirmed bookings for exactly total jamaah
                    $confirmedSeats = $totalJamaah;
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => $confirmedSeats,
                        'status' => 'confirmed',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'confirmation_code' => fake()->bothify('CF####??'),
                        'booking_date' => now(),
                        'confirmed_at' => now(),
                    ]);
                    break;
                    
                case 'excess_confirmed':
                    // Create confirmed bookings for more than total jamaah
                    $confirmedSeats = fake()->numberBetween($totalJamaah + 1, $totalJamaah + 10);
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => $confirmedSeats,
                        'status' => 'confirmed',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'confirmation_code' => fake()->bothify('CF####??'),
                        'booking_date' => now(),
                        'confirmed_at' => now(),
                    ]);
                    break;
                    
                case 'with_cancelled':
                    // Create mix of confirmed and cancelled bookings
                    $confirmedSeats = fake()->numberBetween(1, $totalJamaah - 1);
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => $confirmedSeats,
                        'status' => 'confirmed',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'confirmation_code' => fake()->bothify('CF####??'),
                        'booking_date' => now(),
                        'confirmed_at' => now(),
                    ]);
                    
                    // Add cancelled booking (should not count)
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => fake()->numberBetween(5, 20),
                        'status' => 'cancelled',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'booking_date' => now(),
                    ]);
                    break;
                    
                case 'pending_only':
                    // Create only pending bookings (not confirmed)
                    FlightBooking::create([
                        'id_flight' => $flight->id,
                        'id_keberangkatan' => $keberangkatan->id,
                        'seat_count' => $totalJamaah,
                        'status' => 'pending',
                        'booking_reference' => fake()->bothify('BK####??'),
                        'booking_date' => now(),
                    ]);
                    break;
            }
            
            // Property assertion: Check if stage should be complete
            $hasAllFlightTicketsConfirmed = $keberangkatan->hasAllFlightTicketsConfirmed();
            
            // Expected result based on scenario
            $expectedComplete = false;
            
            switch ($scenario) {
                case 'no_bookings':
                    $expectedComplete = false; // No jamaah means not complete
                    $this->assertFalse($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with no bookings should NOT have all flight tickets confirmed");
                    break;
                    
                case 'partial_confirmed':
                    $expectedComplete = false; // Not enough confirmed tickets
                    $this->assertFalse($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with {$confirmedSeats}/{$totalJamaah} confirmed seats should NOT be complete");
                    break;
                    
                case 'all_confirmed':
                    $expectedComplete = true; // Exactly enough confirmed tickets
                    $this->assertTrue($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with {$confirmedSeats}/{$totalJamaah} confirmed seats SHOULD be complete");
                    break;
                    
                case 'excess_confirmed':
                    $expectedComplete = true; // More than enough confirmed tickets
                    $this->assertTrue($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with {$confirmedSeats}/{$totalJamaah} confirmed seats (excess) SHOULD be complete");
                    break;
                    
                case 'with_cancelled':
                    $expectedComplete = false; // Cancelled bookings don't count
                    $this->assertFalse($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with {$confirmedSeats}/{$totalJamaah} confirmed seats (with cancelled) should NOT be complete");
                    break;
                    
                case 'pending_only':
                    $expectedComplete = false; // Pending bookings don't count
                    $this->assertFalse($hasAllFlightTicketsConfirmed,
                        "Keberangkatan with only pending bookings should NOT be complete");
                    break;
            }
            
            // Verify the property holds
            $this->assertEquals($expectedComplete, $hasAllFlightTicketsConfirmed,
                "Flight tickets stage completion should match expected result for scenario: {$scenario}");
            
            // Additional verification: Check the actual confirmed seat count
            $actualConfirmedSeats = $keberangkatan->flightBookings()
                ->where('status', 'confirmed')
                ->sum('seat_count');
            
            $this->assertEquals($confirmedSeats, $actualConfirmedSeats,
                "Confirmed seat count should match expected value");
            
            // Verify that cancelled and pending bookings are not counted
            if ($scenario === 'with_cancelled' || $scenario === 'pending_only') {
                $totalBookedSeats = $keberangkatan->flightBookings()->sum('seat_count');
                $this->assertGreaterThanOrEqual($actualConfirmedSeats, $totalBookedSeats,
                    "Total booked seats should be >= confirmed seats");
            }
            
            // Clean up for next iteration
            // Delete in correct order to respect foreign keys
            \DB::table('jamaah_bookings')->where('id_keberangkatan', $keberangkatan->id)->delete();
            \DB::table('flight_bookings')->where('id_keberangkatan', $keberangkatan->id)->delete();
            $keberangkatan->delete();
            $flight->delete();
            $package->hppCalculation()->delete();
            $package->delete();
            $outlet->delete();
        }
        
        // Clean up jamaah_bookings table at the end
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('jamaah_bookings');
        Schema::enableForeignKeyConstraints();
    }
}
