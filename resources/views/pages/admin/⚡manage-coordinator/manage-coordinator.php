<?php

use App\Models\Request as ResourceRequest;
use App\Models\Resource;
use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search       = '';

    // ✅ NEW: Reject dialog state
    public ?int   $rejectingRequestId = null;
    public string $rejectReason       = '';

    /**
     * Requests from Program Head accounts only land here for Admin's review.
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

    // ============================================================
    // APPROVE
    // ============================================================
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

        $needed = [];
        foreach ($request->items as $item) {
            $resource = $item->resource_id
                ? Resource::find($item->resource_id)
                : Resource::whereRaw('LOWER(resource_name) = ?', [strtolower($item->item_name)])->first();

            if (!$resource) continue;
            if ($facilityTypeId && $resource->resource_type_id == $facilityTypeId) continue;

            if (!isset($needed[$resource->id])) {
                $needed[$resource->id] = 0;
            }
            $needed[$resource->id] += (int) $item->quantity;
        }

        try {
            DB::transaction(function () use ($request, $needed) {
                $fresh = ResourceRequest::lockForUpdate()->find($request->id);

                if (!$fresh || $fresh->status !== 'pending') {
                    throw new \RuntimeException('This request has already been processed.');
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

                // ✅ Record approval
                \App\Models\RequestApproval::create([
                    'request_id'  => $fresh->id,
                    'approver_id' => Auth::id(),
                    'status'      => 'approved',
                    'remarks'     => null,
                    'approved_at' => now(),
                ]);

                Notification::create([
                    'user_id'    => $fresh->user_id,
                    'request_id' => $fresh->id,
                    'message'    => Auth::user()->name . ' (Admin) approved your request.',
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

    // ============================================================
    // ✅ REJECT WITH REASON
    // ============================================================
    public function openReject(int $id): void
    {
        $this->rejectingRequestId = $id;
        $this->rejectReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingRequestId = null;
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        if (! $this->rejectingRequestId) return;

        $request = $this->incomingRequestsQuery()->findOrFail($this->rejectingRequestId);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            $this->cancelReject();
            return;
        }

        // 1. Mark rejected
        $request->update(['status' => 'rejected']);

        // 2. Save rejection row with reason
        \App\Models\RequestApproval::create([
            'request_id'  => $request->id,
            'approver_id' => Auth::id(),
            'status'      => 'rejected',
            'remarks'     => $this->rejectReason ?: null,
            'approved_at' => now(),
        ]);

        // 3. Notify requester
        $message = Auth::user()->name . ' (Admin) rejected your request.';
        if ($this->rejectReason) {
            $message .= ' Reason: ' . $this->rejectReason;
        }

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => $message,
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);

        session()->flash('message', 'Request rejected and requester notified.');

        $this->cancelReject();
    }

    public function clearFilters()
    {
        $this->reset('search');
    }
};
