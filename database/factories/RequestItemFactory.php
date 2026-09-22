<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'resource_id' => Resource::factory(),
            'item_name' => fake()->words(2, true),
            'quantity' => fake()->numberBetween(1, 20),
            'request_date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'start_time' => fake()->time('H:i:s'),
            'end_time' => fake()->time('H:i:s'),
        ];
    }
}
