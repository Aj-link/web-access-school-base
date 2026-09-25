<?php

namespace App\Livewire\Coordinator;

use App\Models\Notification;
use App\Models\Request as ResourceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    /**
     * Base query scope shared by the listing and the action guards.
     * Only facility reservations (type 1) submitted by Student/Faculty
     * users in this Program Head's own department. Requests submitted
     * by another Program Head are explicitly excluded — this coordinator
     * only reviews their own department's students, never other
     * program heads.
     */
    protected function scopedQuery()
    {
        return ResourceRequest::where('request_type_id', 1)
            ->whereHas('user', function ($q) {
                $q->where('department_id', Auth::user()->department_id) // same department only
                  ->whereDoesntHave('roles', function ($role) {
                      $role->where('name', 'program head'); // exclude program head submissions
                  });
            });
    }

    #[Computed]
    public function reservations()
    {
        return $this->scopedQuery()
            ->with(['user.department', 'items'])
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        // Guard: only ever touch a facility reservation (type 1) from a
        // student/faculty user in this coordinator's own department.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'approved']);

        $facilityItem = $request->items->firstWhere('resource_id', null);
        $approverName = Auth::user()->name;

        $details = $facilityItem
        ? "{$facilityItem->item_name} on" . Carbon::parse($facilityItem->request_date)->
        format('M d, Y') . "({$facilityItem->start_time} - {$facilityItem->end_time})" : $request->purpose;

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => "{$approverName} approved your facility reservation for {$details}.",
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }

    public function reject(int $id)
    {
        // Guard: only ever touch a facility reservation (type 1) from a
        // student/faculty user in this coordinator's own department.
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'rejected']);

        $facilityItem = $request->items->firstWhere('resource_id', null);
        $approverName = Auth::user()->name;

        $details = $facilityItem
            ? "{$facilityItem->item_name} on "
                . Carbon::parse($facilityItem->request_date)->format('M d, Y')
                . " ({$facilityItem->start_time} - {$facilityItem->end_time})"
            : $request->purpose;



        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => "{$approverName} rejected your facility reservation for {$details}.",
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }
};
