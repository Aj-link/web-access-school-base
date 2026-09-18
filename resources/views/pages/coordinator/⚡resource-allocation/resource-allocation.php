<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.coordinator')] class extends Component
{
    use WithPagination;

    // Which request's reject-remarks box is currently open (request_id or null)
    public ?int $rejectingRequestId = null;
    public string $rejectRemarks = '';

    public function mount()
    {
        // ✅ Restrict this page to Program Head only
        if (!Auth::user()->hasRole('program head')) {
            abort(403, 'Only Program Heads can view department resources.');
        }
    }

    /**
     * Format a raw pcs quantity according to the resource's unit.
     */
    protected function formatQuantity(int $qtyInPcs, ?string $unit): string
    {

        $unit = trim($unit ?? '') ?: 'Ream';

        return "{$qtyInPcs} {$unit}";
    }

    /**
     * ✅ NEW: does a given facility+date+time overlap an already-approved
     * reservation for the same facility? Used both to warn on the pending
     * list and to hard-block approval.
     */
    protected function facilityHasConflict(string $facilityName, string $date, string $startTime, string $endTime, int $excludeRequestId): bool
    {
        return DB::table('request_items as ri')
            ->join('requests as req', 'req.id', '=', 'ri.request_id')
            ->where('req.status', 'approved')
            ->where('req.id', '!=', $excludeRequestId)
            ->whereNull('ri.resource_id') // facility line only
            ->where('ri.item_name', $facilityName)
            ->whereDate('ri.request_date', $date)
            ->where('ri.start_time', '<', $endTime)
            ->where('ri.end_time', '>', $startTime)
            ->exists();
    }

    #[Computed]
    public function materialsSummary()
    {
        $departmentId = Auth::user()->department_id;

        return DB::table('resource_all_locations as ral')
            ->join('resources as r', 'r.id', '=', 'ral.resource_id')
            ->join('resource_types as rt', 'rt.id', '=', 'r.resource_type_id')
            ->where('ral.department_id', $departmentId)
            ->where('ral.allocated_quantity', '>', 0)
            ->where('rt.type_name', '!=', 'Facility')
            ->select('r.resource_name', 'ral.allocated_quantity', 'r.unit')
            ->orderByDesc('ral.allocated_quantity')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $row->formatted_quantity = $this->formatQuantity(
                    (int) $row->allocated_quantity,
                    $row->unit
                );
                return $row;
            });
    }

    #[Computed]
public function allocations()
{
    $paginator = DB::table('resource_all_locations as ral')
        ->join('resources as r', 'r.id', '=', 'ral.resource_id')
        ->join('resource_types as rt', 'rt.id', '=', 'r.resource_type_id')
        ->where('ral.department_id', Auth::user()->department_id)
        ->where('rt.type_name', '!=', 'Facility')
        ->select('ral.id', 'ral.resource_id', 'ral.allocated_quantity', 'r.resource_name', 'r.unit')
        ->orderByDesc('ral.updated_at')
        ->paginate(10);

    return $paginator->through(function ($row) {
        $row->formatted_quantity = $this->formatQuantity(
            (int) $row->allocated_quantity,
            $row->unit
        );
        return $row;
    });
}

    #[Computed]
    public function totalAllocated()
    {
        return DB::table('resource_all_locations as ral')
        ->join('resources as r', 'r.id', '=', 'ral.resource_id')
        ->join('resource_types as rt', 'rt.id', '=', 'r.resource_type_id')
        ->where('ral.department_id', Auth::user()->department_id)
        ->where('rt.type_name', '!=', 'Facility')
        ->sum('ral.allocated_quantity');
    }

    // Pending MATERIAL line items (resource_id set) from this department.
    // Note: this deliberately excludes the facility line itself — see
    // pendingFacilityRequests() below for those.
    #[Computed]
    public function pendingMaterialRequests()
    {
        $allocatedByResource = DB::table('resource_all_locations')
            ->where('department_id', Auth::user()->department_id)
            ->pluck('allocated_quantity', 'resource_id');

        return DB::table('request_items as ri')
            ->join('requests as req', 'req.id', '=', 'ri.request_id')
            ->join('resources as r', 'r.id', '=', 'ri.resource_id')
            ->join('users as u', 'u.id', '=', 'req.user_id')
            ->where('req.department_id', Auth::user()->department_id)
            ->where('req.status', 'pending')
            ->whereNotNull('ri.resource_id')
            ->select(
                'req.id as request_id',
                'ri.resource_id',
                'ri.item_name',
                'ri.quantity as requested_quantity',
                'u.name as requester_name',
                'r.unit',
            )
            ->get()
            ->map(function ($item) use ($allocatedByResource) {
                $item->available = $allocatedByResource[$item->resource_id] ?? 0;
                $item->enough    = $item->available >= $item->requested_quantity;

                $item->requested_formatted = $this->formatQuantity(
                    (int) $item->requested_quantity,
                    $item->unit,
                );
                $item->available_formatted = $this->formatQuantity(
                    (int) $item->available,
                    $item->unit
                );

                return $item;
            });
    }

    /**
     * ✅ NEW: Pending FACILITY reservations (the room/facility line item
     * itself, resource_id is null). Previously these never appeared
     * anywhere for the Program Head to act on if the reservation had no
     * materials attached — this surfaces them and flags scheduling
     * conflicts against already-approved bookings before you click Approve.
     */
    #[Computed]
    public function pendingFacilityRequests()
    {
        return DB::table('request_items as ri')
            ->join('requests as req', 'req.id', '=', 'ri.request_id')
            ->join('users as u', 'u.id', '=', 'req.user_id')
            ->where('req.department_id', Auth::user()->department_id)
            ->where('req.status', 'pending')
            ->where('req.request_type_id', 1) // facility reservations only
            ->whereNull('ri.resource_id')      // the facility line itself
            ->select(
                'req.id as request_id',
                'ri.item_name as facility_name',
                'ri.request_date',
                'ri.start_time',
                'ri.end_time',
                'req.purpose',
                'u.name as requester_name'
            )
            ->get()
            ->map(function ($item) {
                $item->has_conflict = $this->facilityHasConflict(
                    $item->facility_name,
                    $item->request_date,
                    $item->start_time,
                    $item->end_time,
                    $item->request_id
                );
                return $item;
            });
    }

    /**
     * ✅ Approve a request (facility, material, or both):
     *   - If it has a facility line, block approval if that room/time
     *     overlaps an already-approved booking (double-booking guard).
     *   - If it has material line items, deduct them from the department's
     *     allocated stock — two-pass so a multi-item request never
     *     deducts partially.
     */
    public function approveRequest(int $requestId): void
    {
        $departmentId = Auth::user()->department_id;

        $request = DB::table('requests')->where('id', $requestId)->first();

        if (!$request || (int) $request->department_id !== (int) $departmentId) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $items = DB::table('request_items')->where('request_id', $requestId)->get();

        $materialItems = $items->whereNotNull('resource_id');
        $facilityItem  = $items->whereNull('resource_id')->first();

        try {
            DB::transaction(function () use ($materialItems, $facilityItem, $departmentId, $requestId, $request) {

                // ✅ FIX: block approval if the room is already booked for an
                // overlapping time on that date.
                if ($facilityItem) {
                    $conflict = $this->facilityHasConflict(
                        $facilityItem->item_name,
                        $facilityItem->request_date,
                        $facilityItem->start_time,
                        $facilityItem->end_time,
                        $requestId
                    );

                    if ($conflict) {
                        throw new \RuntimeException(
                            "\"{$facilityItem->item_name}\" is already booked for an overlapping time on " .
                            \Carbon\Carbon::parse($facilityItem->request_date)->format('M d, Y') .
                            ". Reject this request or ask the requester to choose another slot."
                        );
                    }
                }

                // Pass 1: validate every material item has enough allocated stock
                foreach ($materialItems as $item) {
                    $allocation = DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->lockForUpdate()
                        ->first();

                    if (!$allocation || $allocation->allocated_quantity < $item->quantity) {
                        throw new \RuntimeException(
                            "Not enough stock for \"{$item->item_name}\". Please request a restock from Admin first."
                        );
                    }
                }

                // Pass 2: deduct, now that we know every item can be fulfilled
                foreach ($materialItems as $item) {
                    DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->decrement('allocated_quantity', $item->quantity);
                }

                DB::table('requests')->where('id', $requestId)->update([
                    'status'     => 'approved',
                    'updated_at' => now(),
                ]);

                DB::table('request_approvals')->updateOrInsert(
                    ['request_id' => $requestId, 'approver_id' => Auth::id()],
                    [
                        'status'      => 'approved',
                        'remarks'     => null,
                        'approved_at' => now(),
                        'updated_at'  => now(),
                        'created_at'  => now(),
                    ]
                );
            });

            session()->flash('message', 'Request approved.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }

        unset(
            $this->allocations,
            $this->pendingMaterialRequests,
            $this->pendingFacilityRequests,
            $this->totalAllocated,
            $this->materialsSummary
        );
    }

    public function openReject(int $requestId): void
    {
        $this->rejectingRequestId = $requestId;
        $this->rejectRemarks = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingRequestId = null;
        $this->rejectRemarks = '';
    }

    public function confirmReject(): void
    {
        if (!$this->rejectingRequestId) {
            return;
        }

        $departmentId = Auth::user()->department_id;
        $request = DB::table('requests')->where('id', $this->rejectingRequestId)->first();

        if (!$request || (int) $request->department_id !== (int) $departmentId) {
            abort(403);
        }

        DB::table('requests')->where('id', $this->rejectingRequestId)->update([
            'status'     => 'rejected',
            'updated_at' => now(),
        ]);

        DB::table('request_approvals')->updateOrInsert(
            ['request_id' => $this->rejectingRequestId, 'approver_id' => Auth::id()],
            [
                'status'      => 'rejected',
                'remarks'     => $this->rejectRemarks ?: null,
                'approved_at' => now(),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        session()->flash('message', 'Request rejected.');

        $this->rejectingRequestId = null;
        $this->rejectRemarks = '';

        unset(
            $this->allocations,
            $this->pendingMaterialRequests,
            $this->pendingFacilityRequests,
            $this->totalAllocated,
            $this->materialsSummary
        );
    }
};
