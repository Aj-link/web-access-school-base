<?php

namespace App\Livewire\Coordinator\RequestToAdmin;

use App\Models\Request as ResourceRequest;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
    public ResourceRequest $requestModel;

    public function mount(int $id): void
    {
        $request = ResourceRequest::with([
            'items',
            'requestType',
            'department',
            'approvals.approver',
        ])->findOrFail($id);

        if ($request->user_id !== Auth::id()) {
            abort(403, 'You do not own this request.');
        }

        $this->requestModel = $request;
    }

    public function statusBadgeClasses(string $status): string
    {
        return match ($status) {
            'approved'  => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-400',
            'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            default     => 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-300',
        };
    }

    public function back()
    {
        return redirect()->route('coordinator.request-to-admin.view-request');
    }
};
