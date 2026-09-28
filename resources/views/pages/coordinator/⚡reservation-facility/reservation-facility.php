<?php

use App\Models\Notification;
use App\Models\Request as ResourceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    protected function scopedQuery()
    {
        return ResourceRequest::where('request_type_id', 1)
            ->whereHas('user', function ($q) {
                $q->where('department_id', Auth::user()->department_id)
                  ->whereDoesntHave('roles', function ($role) {
                      $role->where('name', 'program head');
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
        $request = $this->scopedQuery()->with('items')->findOrFail($id);

        // Guard 1 — still pending?
        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        // Guard 2 — double-booking?
        $facilityItem = $request->items->firstWhere('resource_id', null);

        if ($facilityItem && $this->facilityHasConflict(
            $facilityItem->item_name,
            $facilityItem->request_date,
            $facilityItem->start_time,
            $facilityItem->end_time,
            $request->id
        )) {
            session()->flash('error', 'This facility is already booked for an overlapping time. Please reject this request or ask the requester to choose another slot.');
            return;
        }

        $departmentId = Auth::user()->department_id;
        $approverName = Auth::user()->name;

        // ── NEW: deduct attached materials from the department's allocation ──
        $materialItems = $request->items->whereNotNull('resource_id');

        try {
            DB::transaction(function () use ($request, $materialItems, $departmentId) {

                // Pass 1 — validate every material has enough allocated stock
                foreach ($materialItems as $item) {
                    $allocation = DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->lockForUpdate()
                        ->first();

                    if (!$allocation || $allocation->allocated_quantity < $item->quantity) {
                        throw new \RuntimeException(
                            "Not enough allocated stock for \"{$item->item_name}\". Please request a restock from Admin first."
                        );
                    }
                }

                // Pass 2 — deduct from department allocation
                foreach ($materialItems as $item) {
                    DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->decrement('allocated_quantity', $item->quantity);
                }

                // Mark the request approved
                $request->update(['status' => 'approved']);

                // Record the approval
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
            });
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
            return;
        }

        // Notification (outside the transaction — safe if it fails)
        $details = $facilityItem
            ? "{$facilityItem->item_name} on " . Carbon::parse($facilityItem->request_date)->format('M d, Y')
                . " ({$facilityItem->start_time} - {$facilityItem->end_time})"
            : $request->purpose;

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
        $request = $this->scopedQuery()->with('items')->findOrFail($id);

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

        $facilityItem = $request->items->firstWhere('resource_id', null);
        $approverName = Auth::user()->name;

        $details = $facilityItem
            ? "{$facilityItem->item_name} on " . Carbon::parse($facilityItem->request_date)->format('M d, Y')
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
