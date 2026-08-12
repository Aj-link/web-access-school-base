<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Resource;
use App\Models\ResourceAllLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceAllLocation>
 */
class ResourceAllLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'resource_id'        => Resource::factory(),
        'facility_id'        => Facility::factory(),
        'allocated_quantity' => $this->faker->numberBetween(1, 50),
        ];
    }
}
