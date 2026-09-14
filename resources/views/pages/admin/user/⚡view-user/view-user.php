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
        // ✅ FIX: both branches (student OR admin/program head/faculty)
        // now sit inside the SAME where(function(){...}), so the
        // status = 'approved' check applies to every branch, not just
        // the student one. Previously, orWhereHas(...) escaped the
        // nested closure entirely and matched ANY admin/program
        // head/faculty user regardless of approval status.
        return User::with('roles')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('roles', fn($r) => $r->where('name', 'student'))
                      ->where('status', 'approved');
                })
                ->orWhere(function ($q) {
                    $q->whereHas('roles', fn($r) => $r->whereIn('name', ['admin', 'program head', 'faculty']))
                      ->where('status', 'approved');
                });
            })
            ->select('id', 'name', 'email', 'created_at')
            ->paginate(5);
    }
};
