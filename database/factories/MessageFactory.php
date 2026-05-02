<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'mode' => fake()->randomElement(['superadmin', 'chatbot']),
            'content' => fake()->sentence(10),
            'is_read' => false,
            'read_at' => null,
            'outlet_id' => null,
        ];
    }

    /**
     * Indicate that the message is in superadmin mode.
     */
    public function superadminMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'mode' => 'superadmin',
        ]);
    }

    /**
     * Indicate that the message is in chatbot mode.
     */
    public function chatbotMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'mode' => 'chatbot',
            'receiver_id' => null,
        ]);
    }

    /**
     * Indicate that the message has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
