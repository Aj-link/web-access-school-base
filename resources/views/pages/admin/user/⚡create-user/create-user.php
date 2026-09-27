<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::admin')] class extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $department_id;
    public $role = 'program head';

    #[Computed()]
    public function departments()
    {
        return Department::orderBy('department_name')->get(['id', 'department_name']);
    }

    /**
     * Role options. Admin is not listed here — admins are seeded, not created
     * from this page. Faculty covers both teaching and staff positions.
     */
    #[Computed()]
    public function roles()
    {
        return [
            'program head' => 'Program Head',
            'faculty'      => 'Faculty',
            'student'      => 'Student',
        ];
    }

    protected function rules()
    {
        return [
            'name'            => 'required|string|min:3',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6|confirmed',
            'department_id'   => 'required|exists:departments,id',
            'role'            => 'required|in:program head,faculty,student',
        ];
    }

    protected $messages = [
        'name.required'          => 'Please enter the user\'s full name.',
        'name.min'               => 'Name must be at least 3 characters.',
        'email.required'         => 'Please enter an email address.',
        'email.email'            => 'Please enter a valid email address.',
        'email.unique'           => 'This email address is already registered.',
        'password.required'      => 'Please enter a password.',
        'password.min'           => 'Password must be at least 6 characters.',
        'password.confirmed'     => 'Password confirmation does not match.',
        'department_id.required' => 'Please select a department.',
        'department_id.exists'   => 'Selected department is invalid.',
        'role.required'          => 'Please select a role.',
        'role.in'                => 'Selected role is invalid.',
    ];

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name'          => $this->name,
            'email'         => $this->email,
            'password'      => Hash::make($this->password),
            'department_id' => $this->department_id,
            'status'        => 'approved',
        ]);

        $user->assignRole($this->role);

        $roleLabel = $this->roles[$this->role] ?? ucfirst($this->role);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'department_id']);
        $this->role = 'program head';

        session()->flash('success', "{$roleLabel} created successfully!");
    }
};
