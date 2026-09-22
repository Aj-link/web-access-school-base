<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestApprovalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'approver_id' => User::factory(),
            'status' => 'pending',
            'remarks' => null,
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'remarks' => fake()->sentence(),
            'approved_at' => now(),
        ]);
    }
}
