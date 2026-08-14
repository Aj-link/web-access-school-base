<?php

namespace App\Livewire\Pages;

use App\Models\Department;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $department_id = '';
    public string $role = '';
    public bool $terms = false;

    protected array $rules = [
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email|ends_with:@csav.edu.ph',
        'password'      => 'required|string|min:6|confirmed',
        'department_id' => 'required|exists:departments,id',
        'role'          => 'required|in:student,faculty,program head',
        'terms'         => 'accepted',
    ];

    protected array $messages = [
        'email.unique'            => 'This email address is already registered. Please log in instead.',
        'email.ends_with'         => 'Only @csav.edu.ph email addresses are allowed.',
        'email.required'          => 'Please enter your email address.',
        'email.email'             => 'Please enter a valid email address.',
        'name.required'           => 'Please enter your full name.',
        'password.min'            => 'Password must be at least 6 characters.',
        'password.confirmed'      => 'Passwords do not match.',
        'department_id.required'  => 'Please select your department.',
        'role.required'           => 'Please select your role.',
        'terms.accepted'          => 'You must agree to the Terms of Service.',
    ];

    #[Computed]
    public function departments()
    {
        return Department::orderBy('department_name')->get();
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name'          => $this->name,
            'email'         => $this->email,
            'password'      => Hash::make($this->password),
            'department_id' => $this->department_id,
            'status'        => 'pending',
        ]);

        $user->assignRole($this->role);

        // Notify all admins – corrected status and type
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "New {$this->role} registration pending approval: {$this->name}",
                'type'    => 'Gmail',
                'status'  => 'pending',
            ]);
        }

        Auth::login($user);
        session()->regenerate();

        return redirect()->route('waiting');
    }
};
