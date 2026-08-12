<?php

namespace App\Livewire\Admin;

use App\Models\Request as ResourceRequest;
use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public $statusFilter = 'pending';
    public $search = '';

    #[Computed]
    public function requests()
    {
        $query = ResourceRequest::with(['user', 'department', 'requestType', 'items'])
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($role) {
                    $role->where('name', 'coordinator');
                });
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('purpose', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($user) {
                        $user->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('department', function ($dept) {
                        $dept->where('department_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(10);
    }

    #[Computed]
    public function pendingCount()
    {
        return ResourceRequest::whereHas('user', function ($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'coordinator'));
            })
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function approvedCount()
    {
        return ResourceRequest::whereHas('user', function ($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'coordinator'));
            })
            ->where('status', 'approved')
            ->count();
    }

    #[Computed]
    public function rejectedCount()
    {
        return ResourceRequest::whereHas('user', function ($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'coordinator'));
            })
            ->where('status', 'rejected')
            ->count();
    }

    public function approve($id)
    {
        $request = ResourceRequest::findOrFail($id);
        $request->update(['status' => 'approved']);

        Notification::create([
            'user_id' => $request->user_id,
            'message' => 'Your request to admin has been APPROVED.',
            'type'    => 'Gmail',
            'status'  => 'pending',
        ]);

        session()->flash('message', 'Request approved and coordinator notified.');
    }

    public function reject($id)
    {
        $request = ResourceRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        Notification::create([
            'user_id' => $request->user_id,
            'message' => 'Your request to admin has been REJECTED.',
            'type'    => 'Gmail',
            'status'  => 'pending',
        ]);

        session()->flash('message', 'Request rejected and coordinator notified.');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
    }
};
