<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'request_id' => Request::factory(),
            'message' => fake()->sentence(),
            'type' => 'Gmail',
            'status' => fake()->randomElement(['pending', 'sent', 'failed']),
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }
}
