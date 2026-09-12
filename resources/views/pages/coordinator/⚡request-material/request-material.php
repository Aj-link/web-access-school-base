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
     * Scoped by the request's own department_id (set at creation time),
     * not the requestor's current department — so a program head only
     * ever sees requests filed under their own department.
     *
     * Also restricted to requests submitted by Student/Faculty accounts
     * only — Admin or Program Head accounts should never show up here.
     */
    protected function scopedQuery()
    {
        return ResourceRequest::where('request_type_id', 2)
            ->where('department_id', Auth::user()->department_id)
            ->whereHas('user', function ($query) {
                $query->role(['student', 'faculty']);
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
        // Guard: only ever touch a material request (type 2) from this coordinator's own department,
        // and only if it was submitted by a Student/Faculty account.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'approved']);
    }

    public function reject(int $id)
    {
        // Guard: only ever touch a material request (type 2) from this coordinator's own department,
        // and only if it was submitted by a Student/Faculty account.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'rejected']);
    }
};
