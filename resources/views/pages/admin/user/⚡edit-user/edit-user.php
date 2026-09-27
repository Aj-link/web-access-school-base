<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $department_id;
    public $role = 'student';

    public function mount($id)
    {
        $this->user              = User::with('roles')->findOrFail($id);
        $this->name              = $this->user->name;
        $this->email             = $this->user->email;
        $this->department_id     = $this->user->department_id;
        $this->role              = $this->user->roles->first()?->name ?? 'student';
    }

    #[Computed()]
    public function departments()
    {
        return Department::orderBy('department_name')->get(['id', 'department_name']);
    }

    #[Computed()]
    public function isAdmin()
    {
        return $this->user->hasRole('admin');
    }

    /**
     * Role options. Admin is not editable from this page — that's intentional
     * so a mistake here can't lock the admin out.
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
        $rules = [
            'name'  => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ];

        // Only admins bypass the department requirement
        if (! $this->isAdmin) {
            $rules['department_id'] = 'required|exists:departments,id';
            $rules['role']          = 'required|in:program head,faculty,student';
        }

        return $rules;
    }

    protected $messages = [
        'name.required'          => 'Please enter the user\'s full name.',
        'name.min'               => 'Name must be at least 3 characters.',
        'email.required'         => 'Please enter an email address.',
        'email.email'            => 'Please enter a valid email address.',
        'email.unique'           => 'This email address is already registered.',
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

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        // Admins keep no department
        if (! $this->isAdmin) {
            $data['department_id'] = $this->department_id;
        }

        // Only touch the password if a new one was typed
        if (filled($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        // Sync the role if the user isn't an admin
        if (! $this->isAdmin) {
            $this->user->syncRoles([$this->role]);
        }

        $this->reset(['password', 'password_confirmation']);

        session()->flash('success', 'User updated successfully!');
    }
};
