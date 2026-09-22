<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceAllLocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resource_id' => Resource::factory(),
            'department_id' => Department::inRandomOrder()->first()->id,
            'allocated_quantity' => fake()->numberBetween(1, 200),
        ];
    }
}
