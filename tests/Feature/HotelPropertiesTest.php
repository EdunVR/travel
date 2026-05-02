<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class HotelPropertiesTest extends TestCase
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
        
        // Clean up tables
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('outlets');
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
        
        parent::tearDown();
    }

    /**
     * Create the minimal tables required for hotel testing.
     */
    protected function createRequiredTables(): void
    {
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();
        
        // Drop tables if they exist
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('outlets');

        // Create outlets table (required for foreign key)
        Schema::create('outlets', function (Blueprint $table) {
            $table->id('id_outlet');
            $table->string('nama_outlet');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        // Create hotels table
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name');
            $table->string('location');
            $table->string('city');
            $table->string('country');
            $table->integer('star_rating')->unsigned();
            $table->integer('total_rooms')->unsigned();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            
            // Indexes
            $table->index('hotel_name');
            $table->index('city');
            $table->index('id_outlet');
        });
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Feature: hajj-umrah-travel-system, Property 1 (Hotel variant): Hotel Data Round Trip
     * Validates: Requirements 1.3
     * 
     * Property: For any valid hotel data (hotel name, location, star rating, 
     * room types, capacity, contact information), storing it to the database 
     * and then retrieving it should return data equivalent to the original input.
     * 
     * This test runs 100 iterations with random hotel data to verify that
     * hotel persistence is consistent across all valid inputs.
     */
    public function test_hotel_data_round_trip(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Create a test outlet
            $outlet = Outlet::create([
                'nama_outlet' => fake()->company(),
                'alamat' => fake()->address(),
                'telepon' => fake()->phoneNumber(),
            ]);
            
            // Generate random hotel data
            $hotelName = fake()->randomElement([
                'Hilton Makkah Convention Hotel',
                'Swissotel Makkah',
                'Pullman ZamZam Makkah',
                'Fairmont Makkah Clock Royal Tower',
                'Dar Al Eiman Royal Hotel',
                'Elaf Kinda Hotel',
                'Anjum Hotel Makkah',
                'Madinah Marriott Hotel',
                'Crowne Plaza Madinah',
                'Shaza Al Madina'
            ]);
            
            $location = fake()->randomElement([
                'Near Masjid al-Haram',
                'Ajyad Street',
                'Ibrahim Al Khalil Street',
                'King Fahd Road',
                'Near Masjid an-Nabawi',
                'Al Madinah Al Munawwarah',
                'Central Area'
            ]);
            
            $city = fake()->randomElement(['Makkah', 'Madinah', 'Jeddah']);
            $country = 'Saudi Arabia';
            $starRating = fake()->numberBetween(3, 5);
            $totalRooms = fake()->numberBetween(50, 500);
            $contactPerson = fake()->name();
            $phone = fake()->phoneNumber();
            $email = fake()->safeEmail();
            $address = fake()->address();
            
            // Create hotel with generated data
            $hotel = Hotel::create([
                'hotel_name' => $hotelName,
                'location' => $location,
                'city' => $city,
                'country' => $country,
                'star_rating' => $starRating,
                'total_rooms' => $totalRooms,
                'contact_person' => $contactPerson,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'id_outlet' => $outlet->id_outlet,
            ]);
            
            // Property assertion: Hotel should be persisted in database
            $this->assertDatabaseHas('hotels', [
                'id' => $hotel->id,
                'hotel_name' => $hotelName,
                'location' => $location,
                'city' => $city,
                'country' => $country,
                'star_rating' => $starRating,
                'total_rooms' => $totalRooms,
                'contact_person' => $contactPerson,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'id_outlet' => $outlet->id_outlet,
            ]);
            
            // Retrieve the hotel from database
            $retrievedHotel = Hotel::find($hotel->id);
            $this->assertNotNull($retrievedHotel, 'Hotel should be retrievable from database');
            
            // Verify all fields match (round-trip property)
            $this->assertEquals($hotelName, $retrievedHotel->hotel_name, 
                'Hotel name should match after round-trip');
            $this->assertEquals($location, $retrievedHotel->location, 
                'Location should match after round-trip');
            $this->assertEquals($city, $retrievedHotel->city, 
                'City should match after round-trip');
            $this->assertEquals($country, $retrievedHotel->country, 
                'Country should match after round-trip');
            $this->assertEquals($starRating, $retrievedHotel->star_rating, 
                'Star rating should match after round-trip');
            $this->assertEquals($totalRooms, $retrievedHotel->total_rooms, 
                'Total rooms should match after round-trip');
            $this->assertEquals($contactPerson, $retrievedHotel->contact_person, 
                'Contact person should match after round-trip');
            $this->assertEquals($phone, $retrievedHotel->phone, 
                'Phone should match after round-trip');
            $this->assertEquals($email, $retrievedHotel->email, 
                'Email should match after round-trip');
            $this->assertEquals($address, $retrievedHotel->address, 
                'Address should match after round-trip');
            $this->assertEquals($outlet->id_outlet, $retrievedHotel->id_outlet, 
                'Outlet ID should match after round-trip');
            
            // Verify timestamps are set
            $this->assertNotNull($retrievedHotel->created_at, 
                'Created timestamp should be set');
            $this->assertNotNull($retrievedHotel->updated_at, 
                'Updated timestamp should be set');
            
            // Verify the hotel can be updated and retrieved again
            $newTotalRooms = fake()->numberBetween(50, 500);
            $newStarRating = fake()->numberBetween(3, 5);
            $retrievedHotel->update([
                'total_rooms' => $newTotalRooms,
                'star_rating' => $newStarRating
            ]);
            
            $updatedHotel = Hotel::find($hotel->id);
            $this->assertEquals($newTotalRooms, $updatedHotel->total_rooms, 
                'Updated total rooms should persist after round-trip');
            $this->assertEquals($newStarRating, $updatedHotel->star_rating, 
                'Updated star rating should persist after round-trip');
            
            // Verify other fields remain unchanged after update
            $this->assertEquals($hotelName, $updatedHotel->hotel_name, 
                'Hotel name should remain unchanged after partial update');
            $this->assertEquals($location, $updatedHotel->location, 
                'Location should remain unchanged after partial update');
            $this->assertEquals($city, $updatedHotel->city, 
                'City should remain unchanged after partial update');
            $this->assertEquals($contactPerson, $updatedHotel->contact_person, 
                'Contact person should remain unchanged after partial update');
            
            // Clean up for next iteration
            $hotel->delete();
            $outlet->delete();
        }
    }
}
