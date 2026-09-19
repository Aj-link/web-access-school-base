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

    public $statusFilter = 'pending';
    public $search       = '';

    /**
     * Requests from Student/Faculty accounts only land here for Admin's
     * review — Program Head or Admin-authored requests (if any exist)
     * are excluded, since this queue is strictly for reviewing
     * requests filed by students and faculty.
     */
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

        return $query->latest()->paginate(10);
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

    /**
     * Materials currently allocated to each department, built up from
     * approved requests. Powers the "Department Materials" section
     * below the requests table.
     */
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

        $facilityTypeId = DB::table('resource_types')
            ->where('type_name', 'Facility')
            ->value('id');

        // Step 1 — resolve every MATERIAL item to its resource.
        // Facility items (resource_id null, e.g. "LISC", "ROOM 301", or
        // a resource whose type is Facility) are never touched.
        // If the same material appears on more than one line of the
        // request, the quantities are combined so the stock check is
        // done on the total.
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

        // Step 2 — inside ONE transaction: lock the rows, re-check against
        // the CURRENT stock, deduct from central stock, and credit the
        // requester's department (resource_all_locations).
        try {
            DB::transaction(function () use ($request, $needed) {
                // Re-check status under lock so a double-click can't approve twice
                $fresh = ResourceRequest::lockForUpdate()->find($request->id);
                if (!$fresh || $fresh->status !== 'pending') {
                    throw new \RuntimeException('This request has already been processed.');
                }

                foreach ($needed as $resourceId => $qty) {
                    // Lock + read the latest quantity (not a stale value)
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
                    'user_id' => $fresh->user_id,
                    'message' => 'Your request has been approved by the admin.',
                    'type'    => 'Gmail',
                    'status'  => 'pending',
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
            'user_id' => $request->user_id,
            'message' => 'Your request has been rejected by the admin.',
            'type'    => 'Gmail',
            'status'  => 'pending',
        ]);

        session()->flash('message', 'Request rejected and requester notified.');
    }

    public function clearFilters()
    {
        $this->statusFilter = 'pending';
        $this->reset('search');
    }
};
