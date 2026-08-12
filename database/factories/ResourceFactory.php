<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'resource_name'      => $this->faker->word(),
        'description'        => $this->faker->sentence(),
        'quantity_available' => $this->faker->numberBetween(10, 200),
        'status'             => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
