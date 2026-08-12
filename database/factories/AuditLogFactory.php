<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(), // creates a user automatically
            'action'     => $this->faker->randomElement([
                'created', 'updated', 'deleted', 'viewed'
            ]),
            'table_name' => $this->faker->randomElement([
                'users', 'departments', 'facilities', 'resources'
            ]),
            'record_id'  => $this->faker->numberBetween(1, 100),
        ];
    }
}
