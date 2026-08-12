<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::admin')] class extends Component
{
    use WithPagination;

    #[Computed()]
    public function users()
    {
        // Fetch all users with their roles
        return User::with('roles')->where(function($query){
            $query->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->where('status', 'approved');
        })->orWhereHas('roles', fn($r) => $r->whereIn('name', ['admin','coordinator','faculty']))->select('id','name','email','created_at')->paginate(5);
    }
};
