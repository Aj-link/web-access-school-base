<?php

namespace Database\Seeders;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RequestTypeSeeder;
use Database\Seeders\RoleSeeder;
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
    ]);
    }
}
