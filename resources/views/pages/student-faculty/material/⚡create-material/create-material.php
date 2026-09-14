faculty<?php

namespace App\Livewire\StudentFaculty;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public $purpose = '';
    public $items = [
        ['resource_id' => '', 'quantity' => 1],
    ];

    public $availableResources = [];

    protected $rules = [
        'purpose' => 'required|string|min:10|max:500',
        'items' => 'required|array|min:1',
        'items.*.resource_id' => 'required|exists:resources,id',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    protected $messages = [
        'purpose.required' => 'Please state your purpose',
        'purpose.min' => 'Purpose must be at least 10 characters',
        'items.required' => 'Add at least one material item',
        'items.*.resource_id.required' => 'Please select a material',
        'items.*.resource_id.exists' => 'Selected material is invalid.',
        'items.*.quantity.min' => 'Quantity must be at least 1',
    ];

    /**
     * Format a raw pcs quantity according to the resource's unit.
     * e.g. 60 pcs @ Pack(30) -> "2 Packs"
     *      65 pcs @ Pack(30) -> "2 Packs + 5 pcs"
     *      45 pcs @ Pcs      -> "45 Pcs"
     */
    protected function formatQuantity(int $qtyInPcs, ?string $unit, ?int $piecesPerPack): string
    {
        if ($unit === 'Pack' && $piecesPerPack) {
            $packs     = intdiv($qtyInPcs, $piecesPerPack);
            $remainder = $qtyInPcs % $piecesPerPack;

            if ($remainder === 0) {
                return "{$packs} Pack" . ($packs === 1 ? '' : 's');
            }

            return "{$packs} Pack" . ($packs === 1 ? '' : 's') . " + {$remainder} pcs";
        }

        return "{$qtyInPcs} Pcs";
    }

    public function mount()
    {
        $user = Auth::user();

        if ($user->department_id) {
            // ✅ Only show materials actually allocated to the student/faculty's own department
            $this->availableResources = DB::table('resource_all_locations')
                ->join('resources', 'resources.id', '=', 'resource_all_locations.resource_id')
                ->where('resource_all_locations.department_id', $user->department_id)
                ->where('resource_all_locations.allocated_quantity', '>', 0)
                ->where('resources.status', 'available')
                ->orderBy('resources.resource_name')
                ->select(
                    'resources.id as resource_id',
                    'resources.resource_name',
                    'resources.unit',
                    'resources.pieces_per_pack',
                    'resource_all_locations.allocated_quantity'
                )
                ->get()
                ->map(function ($resource) {
                    $resource->available_formatted = $this->formatQuantity(
                        (int) $resource->allocated_quantity,
                        $resource->unit,
                        $resource->pieces_per_pack
                    );
                    return $resource;
                })
                ->toArray();
        }
    }

    /**
     * Look up a resource from the already-loaded $availableResources list
     * (used in the blade for the live Available/Requesting display).
     */
    public function getMaterialResource($resourceId)
    {
        if (!$resourceId) return null;

        return collect($this->availableResources)->firstWhere('resource_id', (int) $resourceId);
    }

    /**
     * How much stock is available for the selected resource, formatted
     * ("45 Pcs" or "3 Packs + 5 pcs"), scoped to this department's allocation.
     */
    public function getAvailableStock($resourceId): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource) {
            return null;
        }

        return $resource->available_formatted;
    }

    /**
     * Real-time "Requesting: 1 Pack + 20 pcs" (or "50 Pcs") breakdown shown
     * under the quantity input as the requester types. Quantity is always
     * typed in raw Pcs — this is just a live preview.
     */
    public function getQuantityBreakdown($resourceId, $quantity): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource) {
            return null;
        }

        $qty = (int) $quantity;

        if ($qty <= 0) {
            return null;
        }

        return $this->formatQuantity($qty, $resource->unit, $resource->pieces_per_pack);
    }

    /**
     * Real-time check: does the typed quantity exceed what's allocated to
     * this department?
     */
    public function getStockWarning($resourceId, $quantity): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource || $quantity === '' || $quantity === null) {
            return null;
        }

        $qty = (int) $quantity;

        if ($qty > (int) $resource->allocated_quantity) {
            return "Exceeds available stock ({$resource->available_formatted})";
        }

        return null;
    }

    public function addItem()
    {
        $this->items[] = ['resource_id' => '', 'quantity' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();

        // Guard: department must be set
        if (!$user->department_id) {
            session()->flash('error', 'Your account has no department assigned. Please contact admin.');
            return;
        }

        // Validate requested quantity against department allocation
        foreach ($this->items as $i => $item) {
            $allocation = DB::table('resource_all_locations')
                ->join('resources', 'resources.id', '=', 'resource_all_locations.resource_id')
                ->where('resource_all_locations.resource_id', $item['resource_id'])
                ->where('resource_all_locations.department_id', $user->department_id)
                ->select('resource_all_locations.allocated_quantity', 'resources.unit', 'resources.pieces_per_pack')
                ->first();

            if (!$allocation || (int) $item['quantity'] > $allocation->allocated_quantity) {
                $available = $allocation
                    ? $this->formatQuantity((int) $allocation->allocated_quantity, $allocation->unit, $allocation->pieces_per_pack)
                    : '0 Pcs';
                $this->addError("items.$i.quantity", "Only {$available} available in your department.");
                return;
            }
        }

        // Create the request
        $request = ResourceRequest::create([
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'request_type_id' => 2, // Material Request
            'purpose' => $this->purpose,
            'status' => 'pending',
            'current_responsibility_center_id' => $user->responsibility_center_id ?? null,
        ]);

        // Create request items
        foreach ($this->items as $item) {
            $resource = DB::table('resources')->where('id', $item['resource_id'])->first();

            RequestItem::create([
                'request_id' => $request->id,
                'resource_id' => $item['resource_id'],
                'item_name' => $resource->resource_name,
                'quantity' => $item['quantity'],
                'request_date' => now()->toDateString(),
                'start_time' => null,
                'end_time' => null,
            ]);
        }

        // Notify all program heads about the new material request
        $programHeads = User::role('program head')->get();
        foreach ($programHeads as $programHead) {
            Notification::create([
                'user_id' => $programHead->id,
                'message' => "New material request from {$user->name}: {$request->items->count()} item(s).",
                'type' => 'Gmail',
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Material request submitted! Waiting for program head approval.');
        return redirect()->route('portal.material');
    }

    public function cancel()
    {
        return redirect()->route('portal.material');
    }
};
