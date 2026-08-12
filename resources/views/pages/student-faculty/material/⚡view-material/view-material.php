<?php

namespace App\Livewire\StudentFaculty;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.student-faculty')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function materialRequests()
    {
        return ResourceRequest::with(['items'])
            ->where('user_id', Auth::id())
            ->where('request_type_id', 2) // Material Request
            ->latest()
            ->paginate(10);
    }

    public function cancelRequest($id)
    {
        $request = ResourceRequest::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if ($request) {
            $request->update(['status' => 'cancelled']);
            session()->flash('message', 'Request cancelled successfully.');
        } else {
            session()->flash('error', 'Request cannot be cancelled.');
        }
    }

    public function deleteRequest($id)
    {
        $request = ResourceRequest::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'cancelled')
            ->first();

        if ($request) {
            RequestItem::where('request_id', $request->id)->delete();
            $request->delete();
            session()->flash('message', 'Request deleted successfully.');
        } else {
            session()->flash('error', 'Only cancelled requests can be deleted.');
        }
    }
};
