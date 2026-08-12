<?php

namespace Database\Factories;

use App\Models\MaterialRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaterialRequest>
 */
class MaterialRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id'        => User::factory(),
        'purpose'        => $this->faker->sentence(),
        'requested_date' => $this->faker->date(),
        'status'         => $this->faker->randomElement(['pending', 'approved', 'rejected', 'returned']),
        ];
    }
}
