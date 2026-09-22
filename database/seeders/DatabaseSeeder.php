<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            DepartmentSeeder::class,
            RequestTypeSeeder::class,
            ResourceSeeder::class,
            DummySeeder::class,
        ]);

        // One program head, 5 faculty, 5 students per department
        Department::all()->each(function (Department $department) {
            User::factory()
                ->programHead()
                ->create(['department_id' => $department->id]);

            User::factory(5)
                ->faculty()
                ->create(['department_id' => $department->id]);

            User::factory(5)
                ->student()
                ->create(['department_id' => $department->id]);
        });
    }
}
