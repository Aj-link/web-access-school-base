<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResponsibilityCenterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Supply Office',
                'Facilities Office',
                'Property Custodian',
                'Budget Office',
                'Admin Office',
                'IT Services Center',
            ]),
        ];
    }
}
