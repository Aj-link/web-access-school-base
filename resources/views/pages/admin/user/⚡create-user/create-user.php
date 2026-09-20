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

    #[Computed()]
    public function departments()
    {
        return Department::orderBy('department_name')->get(['id', 'department_name']);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id,
            'status' => 'approved',
        ]);

        // Program head only
        $user->assignRole('program head');

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'department_id']);

        session()->flash('success', 'Program head created successfully!');
    }
};
