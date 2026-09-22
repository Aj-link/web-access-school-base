<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\RequestType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::inRandomOrder()->first()->id,
            'request_type_id' => RequestType::inRandomOrder()->first()?->id ?? RequestType::factory(),
            'purpose' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'current_responsibility_center_id' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }
}
