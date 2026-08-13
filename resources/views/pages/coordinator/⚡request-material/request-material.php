<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    #[Computed]
    public function requests()
    {
        return ResourceRequest::with(['user.department', 'items'])
            ->where('request_type_id', 2)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->whereHas('user', function ($q) {
                $q->where('department_id', Auth::user()->department_id); // ✅ same department only
            })
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        ResourceRequest::findOrFail($id)->update([
            'status' => 'approved',
        ]);
    }

    public function reject(int $id)
    {
        ResourceRequest::findOrFail($id)->update([
            'status' => 'rejected',
        ]);
    }
};
