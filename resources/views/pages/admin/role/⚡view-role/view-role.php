<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts::admin')] class extends Component
{
    public $selectedRoles = [];
    public $selectAll = false;

    #[Computed()]
    public function roles()
    {
        return Role::with('permissions')
            ->select('id','name','created_at')
            ->get();
    }


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRoles = $this->roles->pluck('id')->toArray();
        } else {
            $this->selectedRoles = [];
        }
    }


    public function updatedSelectedRoles()
    {
        $this->selectAll = count($this->selectedRoles) === $this->roles->count();
    }


    public function deleteSelected()
    {
        if (!empty($this->selectedRoles)) {
            Role::whereIn('id', $this->selectedRoles)->delete();
            $this->selectedRoles = [];
            $this->selectAll = false;
            session()->flash('success', 'Selected roles deleted successfully!');
        }
    }
};
