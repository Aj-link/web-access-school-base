<?php

namespace App\Livewire\Admin;

use App\Models\Request as ResourceRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    #[Computed]
    public function reservations()
    {
        return ResourceRequest::with(['user.department', 'items'])
            ->where('request_type_id', 1)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        $request = ResourceRequest::with('items')->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $facilityItem = $request->items->firstWhere('resource_id', null);

        if ($facilityItem && $this->facilityHasConflict(
            $facilityItem->item_name,
            $facilityItem->request_date,
            $facilityItem->start_time,
            $facilityItem->end_time,
            $request->id
        )) {
            session()->flash('error', 'This facility is already booked for an overlapping time.');
            return;
        }

        $request->update(['status' => 'approved']);

        // ── Record the approval ──
        DB::table('request_approvals')->updateOrInsert(
            ['request_id' => $request->id, 'approver_id' => Auth::id()],
            [
                'status'      => 'approved',
                'remarks'     => null,
                'approved_at' => now(),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => 'Your facility reservation has been approved by the admin.',
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }

    public function reject(int $id)
    {
        $request = ResourceRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $request->update(['status' => 'rejected']);

        // ── Record the rejection ──
        DB::table('request_approvals')->updateOrInsert(
            ['request_id' => $request->id, 'approver_id' => Auth::id()],
            [
                'status'      => 'rejected',
                'remarks'     => null,
                'approved_at' => now(),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => 'Your facility reservation has been rejected by the admin.',
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }

    protected function facilityHasConflict(
        string $facilityName,
        string $date,
        string $startTime,
        string $endTime,
        int $excludeRequestId
    ): bool {
        return DB::table('request_items as ri')
            ->join('requests as req', 'req.id', '=', 'ri.request_id')
            ->where('req.status', 'approved')
            ->where('req.id', '!=', $excludeRequestId)
            ->whereNull('ri.resource_id')
            ->where('ri.item_name', $facilityName)
            ->whereDate('ri.request_date', $date)
            ->where('ri.start_time', '<', $endTime)
            ->where('ri.end_time', '>', $startTime)
            ->exists();
    }
};
