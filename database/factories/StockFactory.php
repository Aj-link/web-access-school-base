<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    public function definition(): array
    {
        $before = fake()->numberBetween(0, 500);
        $added = fake()->numberBetween(1, 200);

        return [
            'resource_id' => Resource::factory(),
            'user_id' => User::factory(),
            'quantity_added' => $added,
            'quantity_before' => $before,
            'quantity_after' => $before + $added,
            'supplier' => fake()->company(),
            'unit_price' => fake()->randomFloat(2, 5, 500),
            'arrival_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'arrival_time' => fake()->time('H:i:s'),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
