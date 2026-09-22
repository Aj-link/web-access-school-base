<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted', 'approved', 'rejected']),
            'table_name' => fake()->randomElement(['requests', 'resources', 'stocks', 'users', 'reports']),
            'record' => [
                'id' => fake()->numberBetween(1, 1000),
                'changes' => fake()->words(3),
            ],
        ];
    }
}
