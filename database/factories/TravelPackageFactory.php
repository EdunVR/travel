<?php

namespace Database\Factories;

use App\Models\TravelPackage;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelPackageFactory extends Factory
{
    protected $model = TravelPackage::class;

    public function definition(): array
    {
        $departureDate = $this->faker->dateTimeBetween('+1 month', '+6 months');
        $durationDays = $this->faker->numberBetween(7, 30);
        $returnDate = (clone $departureDate)->modify("+{$durationDays} days");

        return [
            'package_code' => $this->faker->unique()->numerify('PKG####'),
            'package_name' => $this->faker->randomElement(['Umrah', 'Hajj']) . ' ' . $this->faker->city(),
            'package_type' => $this->faker->randomElement(['hajj', 'umrah']),
            'description' => $this->faker->paragraph(),
            'duration_days' => $durationDays,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'capacity' => $this->faker->numberBetween(20, 100),
            'price' => $this->faker->numberBetween(20000000, 50000000),
            'hpp' => $this->faker->numberBetween(15000000, 40000000),
            'profit_margin' => $this->faker->randomFloat(2, 10, 30),
            'status' => $this->faker->randomElement(['draft', 'active', 'full', 'completed', 'cancelled']),
            'current_workflow_stage' => 'product_analysis',
            'id_outlet' => Outlet::factory()
        ];
    }
}
