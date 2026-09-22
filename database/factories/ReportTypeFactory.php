<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReportTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type_name' => fake()->unique()->randomElement([
                'Monthly Utilization Report',
                'Resource Inventory Report',
                'Request Summary Report',
                'Stock Movement Report',
            ]),
        ];
    }
}
