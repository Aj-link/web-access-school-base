<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resource_type_id' => ResourceType::inRandomOrder()->first()?->id ?? ResourceType::factory(),
            'resource_name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'quantity_available' => $this->faker->numberBetween(10, 200),
            'unit' => $this->faker->randomElement(['Ream', 'Piece', 'Box', 'Set']),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
