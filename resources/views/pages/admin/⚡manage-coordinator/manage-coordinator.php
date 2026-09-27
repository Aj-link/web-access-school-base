<?php

use App\Models\Request as ResourceRequest;
use App\Models\Resource;
use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search       = '';

    protected function incomingRequestsQuery()
    {
        return ResourceRequest::whereHas('user', function ($query) {
            $query->role(['program head']);
        });
    }

    #[Computed]
    public function requests()
    {
        $query = $this->incomingRequestsQuery()
            ->with(['user', 'department', 'requestType', 'items.resource']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('purpose', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', '%' . $this->search . '%')
                    )
                    ->orWhereHas('department', fn($d) =>
                        $d->where('department_name', 'like', '%' . $this->search . '%')
                    );
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(5);
    }

    #[Computed]
    public function pendingCount()
    {
        return $this->incomingRequestsQuery()->where('status', 'pending')->count();
    }

    #[Computed]
    public function approvedCount()
    {
        return $this->incomingRequestsQuery()->where('status', 'approved')->count();
    }

    #[Computed]
    public function rejectedCount()
    {
        return $this->incomingRequestsQuery()->where('status', 'rejected')->count();
    }

    #[Computed]
    public function departmentAllocations()
    {
        return DB::table('resource_all_locations')
            ->join('resources', 'resources.id', '=', 'resource_all_locations.resource_id')
            ->join('departments', 'departments.id', '=', 'resource_all_locations.department_id')
            ->where('resource_all_locations.allocated_quantity', '>', 0)
            ->select(
                'resource_all_locations.id',
                'resource_all_locations.allocated_quantity',
                'resource_all_locations.updated_at',
                'resources.resource_name',
                'departments.department_name'
            )
            ->orderByDesc('resource_all_locations.updated_at')
            ->limit(10)
            ->get();
    }

    public function approve($id)
    {
        $request = $this->incomingRequestsQuery()
            ->with('items')
            ->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        // ── FIX: block double-booking on facility reservations ──
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

        $facilityTypeId = DB::table('resource_types')
            ->where('type_name', 'Facility')
            ->value('id');

        // Step 1 — resolve every MATERIAL item to its resource.
        $needed = [];
        foreach ($request->items as $item) {
            $resource = $item->resource_id
                ? Resource::find($item->resource_id)
                : Resource::whereRaw('LOWER(resource_name) = ?', [strtolower($item->item_name)])->first();

            if (!$resource) {
                continue; // facility / non-inventory line item
            }

            if ($facilityTypeId && $resource->resource_type_id == $facilityTypeId) {
                continue; // facility resource
            }

            if (!isset($needed[$resource->id])) {
                $needed[$resource->id] = 0;
            }
            $needed[$resource->id] += (int) $item->quantity;
        }

        // Step 2 — inside ONE transaction: lock, re-check, deduct, credit.
        try {
            DB::transaction(function () use ($request, $needed) {
                // Re-check status under lock so a double-click can't approve twice
                $fresh = ResourceRequest::lockForUpdate()->find($request->id);
                if (!$fresh || $fresh->status !== 'pending') {
                    throw new \RuntimeException('This request has already been processed.');
                }

                // ── Re-check conflict under lock (defends against TOCTOU race) ──
                $facilityItem = $fresh->items()->whereNull('resource_id')->first();
                if ($facilityItem && $this->facilityHasConflict(
                    $facilityItem->item_name,
                    $facilityItem->request_date,
                    $facilityItem->start_time,
                    $facilityItem->end_time,
                    $fresh->id
                )) {
                    throw new \RuntimeException('This facility is already booked for an overlapping time.');
                }

                foreach ($needed as $resourceId => $qty) {
                    $resource = Resource::lockForUpdate()->find($resourceId);

                    if ($resource->quantity_available < $qty) {
                        throw new \RuntimeException(
                            "Cannot approve: only {$resource->quantity_available} unit(s) of \"{$resource->resource_name}\" available, but {$qty} requested."
                        );
                    }

                    $resource->decrement('quantity_available', $qty);

                    $existing = DB::table('resource_all_locations')
                        ->where('resource_id', $resource->id)
                        ->where('department_id', $fresh->department_id)
                        ->first();

                    if ($existing) {
                        DB::table('resource_all_locations')
                            ->where('id', $existing->id)
                            ->update([
                                'allocated_quantity' => $existing->allocated_quantity + $qty,
                                'updated_at'         => now(),
                            ]);
                    } else {
                        DB::table('resource_all_locations')->insert([
                            'resource_id'        => $resource->id,
                            'department_id'      => $fresh->department_id,
                            'allocated_quantity' => $qty,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    }
                }

                $fresh->update(['status' => 'approved']);

                Notification::create([
                    'user_id'    => $fresh->user_id,
                    'request_id' => $fresh->id,
                    'message'    => 'Your request has been approved by the admin.',
                    'type'       => 'Gmail',
                    'status'     => 'pending',
                ]);
            });
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
            return;
        }

        session()->flash('message', 'Request approved. Inventory updated.');
    }

    public function reject($id)
    {
        $request = $this->incomingRequestsQuery()->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $request->update(['status' => 'rejected']);

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => 'Your request has been rejected by the admin.',
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);

        session()->flash('message', 'Request rejected and requester notified.');
    }

    public function clearFilters()
    {
        $this->reset('search');
    }

    /**
     * Does the given facility+date+time overlap an already-approved
     * reservation for the same facility?
     */
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
