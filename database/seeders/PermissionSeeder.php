<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $adminRole       = Role::firstOrCreate(['name' => 'admin']);
        $facultyRole     = Role::firstOrCreate(['name' => 'faculty']);
        $studentRole     = Role::firstOrCreate(['name' => 'student']);
        $programHeadRole = Role::firstOrCreate(['name' => 'program head']);

        // Define permissions
        $permissions = [
            'can_view_any',
            'can_view',
            'can_create',
            'can_update',
            'can_delete',
        ];

        // Seed permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions per role
        $adminRole->syncPermissions($permissions);

        $facultyRole->syncPermissions([
            'can_view',
            'can_create',
        ]);

        $studentRole->syncPermissions([
            'can_view',
            'can_create',
        ]);

        $programHeadRole->syncPermissions([
            'can_view',
            'can_create',
        ]);

        // ✅ Only seed admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@csav.edu.ph'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('123123'),
                'status'   => 'approved',
            ]
        );
        $admin->assignRole($adminRole);
    }
}
