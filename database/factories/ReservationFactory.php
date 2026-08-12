<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    $start = $this->faker->dateTimeBetween('+1 days', '+1 week');
    $end   = (clone $start)->modify('+2 hours');

    return [
        'user_id'     => User::factory(),
        'facility_id' => Facility::factory(),
        'start_time'  => $start,
        'end_time'    => $end,
        'status'      => $this->faker->randomElement(['pending', 'approved', 'cancelled']),
    ];
    }
}
