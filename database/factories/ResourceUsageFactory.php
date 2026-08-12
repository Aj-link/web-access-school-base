<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceUsage>
 */
class ResourceUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'resource_id'   => Resource::factory(),
        'user_id'       => User::factory(),
        'quantity_used' => $this->faker->numberBetween(1, 20),
        'usage_date'    => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
