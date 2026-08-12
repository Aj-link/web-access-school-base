<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['id' => 1, 'type_name' => 'Facility Reservation'],
            ['id' => 2, 'type_name' => 'Material Request'],
        ];

        foreach ($types as $type) {
            \App\Models\RequestType::firstOrCreate(
                ['id' => $type['id']],
                ['type_name' => $type['type_name']]
            );
        }
    }
}
