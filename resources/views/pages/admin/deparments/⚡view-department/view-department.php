<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    #[Computed]
    public function departments()
    {
        return Department::withCount('users')->latest()->get();
    }

    public function delete(int $id)
    {
        Department::findOrFail($id)->delete();
        session()->flash('success', 'Department deleted successfully.');
    }
};
