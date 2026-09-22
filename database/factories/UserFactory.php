<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123123'),
            'status' => 'active',
            'department_id' => Department::inRandomOrder()->first()->id,
            'responsibility_center_id' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign a role to the user after creation.
     */
    public function withRole(string $role): static
    {
        return $this->afterCreating(function (User $user) use ($role) {
            $user->assignRole(Role::firstOrCreate(['name' => $role])->name);
        });
    }

    /**
     * Shortcut: program head.
     */
    public function programHead(): static
    {
        return $this->withRole('program head');
    }

    /**
     * Shortcut: faculty.
     */
    public function faculty(): static
    {
        return $this->withRole('faculty');
    }

    /**
     * Shortcut: student.
     */
    public function student(): static
    {
        return $this->withRole('student');
    }
}
