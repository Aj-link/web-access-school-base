<?php

namespace App\Livewire\Coordinator;

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
     * e.g. 40 pcs @ Pack(40) -> "1 Pack"
     *      20 pcs @ Pack(40) -> "20 pcs"          (less than a full pack — no "0 Packs" prefix)
     *      65 pcs @ Pack(30) -> "2 Packs + 5 pcs"
     *      45 pcs @ Pcs      -> "45 Pcs"
     */
    protected function formatQuantity(int $qtyInPcs, ?string $unit, ?int $piecesPerPack): string
    {
        if ($unit === 'Pack' && $piecesPerPack) {
            $packs     = intdiv($qtyInPcs, $piecesPerPack);
            $remainder = $qtyInPcs % $piecesPerPack;

            // Less than one full pack left — just show raw pcs, no "0 Packs" clutter
            if ($packs === 0) {
                return "{$remainder} pcs";
            }

            if ($remainder === 0) {
                return "{$packs} Pack" . ($packs === 1 ? '' : 's');
            }

            return "{$packs} Pack" . ($packs === 1 ? '' : 's') . " + {$remainder} pcs";
        }

        return "{$qtyInPcs} Pcs";
    }

    /**
     * Compact "at a glance" summary — top materials currently held by this
     * department, for a quick view at the top of the page.
     */
    #[Computed]
    public function materialsSummary()
    {
        $departmentId = Auth::user()->department_id;

        return DB::table('resource_all_locations as ral')
            ->join('resources as r', 'r.id', '=', 'ral.resource_id')
            ->where('ral.department_id', $departmentId)
            ->where('ral.allocated_quantity', '>', 0)
            ->select(
                'r.resource_name',
                'ral.allocated_quantity',
                'r.unit',
                'r.pieces_per_pack'
            )
            ->orderByDesc('ral.allocated_quantity')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $row->formatted_quantity = $this->formatQuantity(
                    (int) $row->allocated_quantity,
                    $row->unit,
                    $row->pieces_per_pack
                );
                return $row;
            });
    }

    #[Computed]
    public function allocations()
    {
        $paginator = DB::table('resource_all_locations as ral')
            ->join('resources as r', 'r.id', '=', 'ral.resource_id')
            ->where('ral.department_id', Auth::user()->department_id)
            ->select(
                'ral.id',
                'ral.resource_id',
                'ral.allocated_quantity',
                'r.resource_name',
                'r.unit',
                'r.pieces_per_pack'
            )
            ->orderByDesc('ral.updated_at')
            ->paginate(10);

        return $paginator->through(function ($row) {
            $row->formatted_quantity = $this->formatQuantity(
                (int) $row->allocated_quantity,
                $row->unit,
                $row->pieces_per_pack
            );
            return $row;
        });
    }

    #[Computed]
    public function totalAllocated()
    {
        return DB::table('resource_all_locations')
            ->where('department_id', Auth::user()->department_id)
            ->sum('allocated_quantity');
    }

    // ✅ Pending material requests from this department waiting on Program Head's
    // approval, cross-checked against what's currently allocated so the
    // Program Head can see availability before deciding.
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
            // Student/faculty submissions sit at 'pending' status waiting
            // directly on the Program Head.
            ->where('req.status', 'pending')
            ->whereNotNull('ri.resource_id')  // material line items only
            ->select(
                'req.id as request_id',
                'ri.resource_id',
                'ri.item_name',
                'ri.quantity as requested_quantity',
                'u.name as requester_name',
                'r.unit',
                'r.pieces_per_pack'
            )
            ->get()
            ->map(function ($item) use ($allocatedByResource) {
                $item->available = $allocatedByResource[$item->resource_id] ?? 0;
                $item->enough    = $item->available >= $item->requested_quantity;

                $item->requested_formatted = $this->formatQuantity(
                    (int) $item->requested_quantity,
                    $item->unit,
                    $item->pieces_per_pack
                );
                $item->available_formatted = $this->formatQuantity(
                    (int) $item->available,
                    $item->unit,
                    $item->pieces_per_pack
                );

                return $item;
            });
    }

    /**
     * ✅ Approve a material request: deduct the requested quantity from the
     * department's allocated stock (resource_all_locations) and mark the
     * request approved. Uses two-pass validation (check everything first,
     * then deduct) so a multi-item request never deducts partially.
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

        $items = DB::table('request_items')
            ->where('request_id', $requestId)
            ->whereNotNull('resource_id')
            ->get();

        try {
            DB::transaction(function () use ($items, $departmentId, $requestId) {
                // Pass 1: validate every item has enough allocated stock
                foreach ($items as $item) {
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
                foreach ($items as $item) {
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

            session()->flash('message', 'Request approved and stock deducted.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }

        unset($this->allocations, $this->pendingMaterialRequests, $this->totalAllocated, $this->materialsSummary);
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

        unset($this->allocations, $this->pendingMaterialRequests, $this->totalAllocated, $this->materialsSummary);
    }
};