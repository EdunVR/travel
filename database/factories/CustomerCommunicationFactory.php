<?php

namespace Database\Factories;

use App\Models\CustomerCommunication;
use App\Models\Member;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerCommunicationFactory extends Factory
{
    protected $model = CustomerCommunication::class;

    public function definition(): array
    {
        return [
            'id_member' => Member::factory(),
            'id_travel_package' => TravelPackage::factory(),
            'communication_method' => $this->faker->randomElement(['phone_call', 'whatsapp', 'email', 'in_person', 'other']),
            'communication_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->optional()->sentence(),
            'follow_up_status' => $this->faker->randomElement(['pending', 'contacted', 'responded', 'no_response']),
            'next_follow_up_date' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'contacted_by' => User::factory()
        ];
    }
}
