<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Computer Studies',
            'Criminology',
            'Arts',
            'Business',
            'Education',
            'Engineering',
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate([
                'department_name' => $department,
            ]);
        }
    }
}
