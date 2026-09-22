<?php

namespace Database\Factories;

use App\Models\ReportType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'report_type_id' => ReportType::inRandomOrder()->first()->id,
            'generated_by' => User::factory(),
        ];
    }
}
