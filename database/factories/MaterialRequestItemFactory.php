<?php

namespace Database\Factories;

use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaterialRequestItem>
 */
class MaterialRequestItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
        'material_request_id' => MaterialRequest::factory(),
        'resource_id'         => Resource::factory(),
        'quantity'            => $this->faker->numberBetween(1, 50),
    ];
    }
}
