<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function logs()
    {
        return AuditLog::with('user')
            ->when($this->search, function ($query) {
                $query->where('action', 'like', '%' . $this->search . '%')
                    ->orWhere('table_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function totalLogs()
    {
        return AuditLog::count();
    }

    #[Computed]
    public function todayLogs()
    {
        return AuditLog::whereDate('created_at', today())->count();
    }
};
