<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::admin')] class extends Component
{
    public $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $department_id;

    public function mount($id)
{
    $this->user = User::findOrFail($id);
    $this->name = $this->user->name;
    $this->email = $this->user->email;
    $this->department_id = $this->user->department_id;
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

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'department_id' => $this->isAdmin
                ? 'nullable'
                : 'required|exists:departments,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Admins have no department, so only update it for everyone else
        if (! $this->isAdmin) {
            $data['department_id'] = $this->department_id;
        }

        // Only change the password if a new one was typed
        if (filled($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        $this->reset(['password', 'password_confirmation']);

        session()->flash('success', 'User updated successfully!');
    }
};
