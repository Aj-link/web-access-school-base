<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::admin')] class extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRole; // single role only

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'selectedRole' => 'required|string'
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Assign only one role
        $user->assignRole($this->selectedRole);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRole']);

        session()->flash('success', 'User created successfully with role assigned!');
    }
};
