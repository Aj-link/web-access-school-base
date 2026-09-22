<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceUsageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'resource_id' => Resource::factory(),
            'quantity_used' => fake()->numberBetween(1, 50),
            'used_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
