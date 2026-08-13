<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    /**
     * Base query scope shared by the listing and the action guards.
     */
    protected function scopedQuery()
    {
        return ResourceRequest::where('request_type_id', 2)
            ->whereHas('user', function ($q) {
                $q->where('department_id', Auth::user()->department_id); // same department only
            });
    }

    #[Computed]
    public function requests()
    {
        return $this->scopedQuery()
            ->with(['user.department', 'items'])
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        // Guard: only ever touch a material request (type 2) from this coordinator's own department.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'approved']);
    }

    public function reject(int $id)
    {
        // Guard: only ever touch a material request (type 2) from this coordinator's own department.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'rejected']);
    }
};
