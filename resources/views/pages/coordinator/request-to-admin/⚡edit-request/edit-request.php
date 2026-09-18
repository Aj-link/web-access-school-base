<?php

namespace App\Livewire\ProgramHead\RequestToAdmin;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\RequestType;
use App\Models\Resource;
use App\Models\ResourceType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
    public ResourceRequest $requestModel;

    public $request_type_id = '';
    public $purpose = '';
    public $request_date = '';

    // Facility fields
    public $facility_name = '';
    public $start_time = '09:00';
    public $end_time = '10:00';
    public array $facilityOptions = [];

    // Materials — used for BOTH: optional add-on to a Facility Reservation,
    // AND as the main list when request_type_id == 2 (Material Request)
    public array $materials = [];
    public $availableResources = [];

    public function mount(int $id)
    {
        $request = ResourceRequest::with('items')->findOrFail($id);

        if ($request->user_id !== Auth::id()) {
            abort(403, 'You do not own this request.');
        }

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request can no longer be edited.');
            redirect()->route('coordinator.request-to-admin.view-request');
            return;
        }

        $this->requestModel = $request;

        $this->request_type_id = $request->request_type_id;
        $this->purpose = $request->purpose;

        $facilityType = ResourceType::where('type_name', 'Facility')->first();

        if ($facilityType) {
            $this->facilityOptions = Resource::where('resource_type_id', $facilityType->id)
                ->where('status', 'available')
                ->orderBy('resource_name')
                ->pluck('resource_name')
                ->toArray();
        } else {
            $this->facilityOptions = Resource::whereHas('resourceType', fn($q) =>
                $q->where('type_name', 'like', '%facility%')
                  ->orWhere('type_name', 'like', '%Facility%')
            )
            ->where('status', 'available')
            ->orderBy('resource_name')
            ->pluck('resource_name')
            ->toArray();
        }

        $this->availableResources = Resource::where('status', 'available')
            ->where('quantity_available', '>', 0)
            ->where(function ($q) {
                $q->whereHas('resourceType', function ($q2) {
                    $q2->where('type_name', '!=', 'Facility')
                       ->where('type_name', 'not like', '%facility%');
                })->orWhereDoesntHave('resourceType');
            })
            ->orderBy('resource_name')
            ->get()
            ->map(function ($resource) {
                $resource->available_formatted = $this->formatQuantity(
                    (int) $resource->quantity_available,
                    $resource->unit,
                    $resource->pieces_per_pack
                );
                return $resource;
            });

        if ($this->request_type_id == 1) {
            $facilityItem = $request->items->firstWhere('resource_id', null);

            if ($facilityItem) {
                $this->facility_name = $facilityItem->item_name;
                $this->start_time    = $facilityItem->start_time;
                $this->end_time      = $facilityItem->end_time;
                $this->request_date  = $facilityItem->request_date;
            }

            $this->materials = $request->items
                ->whereNotNull('resource_id')
                ->map(fn ($item) => [
                    'id'          => $item->id,
                    'resource_id' => $item->resource_id,
                    'quantity'    => $item->quantity,
                ])
                ->values()
                ->toArray();
        }

        if ($this->request_type_id == 2) {
            $firstItem = $request->items->first();
            $this->request_date = $firstItem?->request_date;

            $this->materials = $request->items
                ->map(fn ($item) => [
                    'id'          => $item->id,
                    'resource_id' => $item->resource_id,
                    'quantity'    => $item->quantity,
                ])
                ->values()
                ->toArray();
        }
    }

    protected function rules()
    {
        $rules = [
            'purpose'         => 'required|string|min:10|max:500',
            'request_date'    => 'required|date',
            'materials.*.resource_id' => 'nullable|exists:resources,id',
            'materials.*.quantity'    => 'nullable|integer|min:1',
        ];

        if ($this->request_type_id == 1) {
            $rules['facility_name'] = 'required|string|max:255';
            $rules['start_time']    = 'required';
            $rules['end_time']      = 'required|after:start_time';
        }

        if ($this->request_type_id == 2) {
            $rules['materials'] = 'required|array|min:1';
            $rules['materials.*.resource_id'] = 'required|exists:resources,id';
            $rules['materials.*.quantity']    = 'required|integer|min:1';
        }

        return $rules;
    }

    protected $messages = [
        'purpose.required'         => 'Please state your purpose',
        'purpose.min'              => 'Purpose must be at least 10 characters',
        'request_date.required'    => 'Please select a date',
        'facility_name.required'   => 'Please select a facility',
        'start_time.required'      => 'Please select start time',
        'end_time.required'        => 'Please select end time',
        'end_time.after'           => 'End time must be after start time',
        'materials.required'       => 'Please add at least one material',
        'materials.*.resource_id.required' => 'Please select a material',
        'materials.*.resource_id.exists'   => 'Selected material is invalid.',
        'materials.*.quantity.required'    => 'Please enter quantity',
        'materials.*.quantity.min'         => 'Quantity must be at least 1',
    ];

    #[Computed]
    public function requestTypes()
    {
        return RequestType::all();
    }

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

    public function getMaterialResource($resourceId)
    {
        if (!$resourceId) return null;

        return collect($this->availableResources)->firstWhere('id', (int) $resourceId);
    }

    protected function alreadyHeldQuantity($materialId): int
    {
        if (!$materialId) {
            return 0;
        }

        return RequestItem::find($materialId)?->quantity ?? 0;
    }

    public function getAvailableStock($resourceId, $materialId = null): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource) {
            return null;
        }

        $effectiveAvailable = (int) $resource->quantity_available + $this->alreadyHeldQuantity($materialId);

        return $this->formatQuantity($effectiveAvailable, $resource->unit, $resource->pieces_per_pack);
    }

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

    public function getStockWarning($resourceId, $quantity, $materialId = null): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource || $quantity === '' || $quantity === null) {
            return null;
        }

        $qty = (int) $quantity;
        $effectiveAvailable = (int) $resource->quantity_available + $this->alreadyHeldQuantity($materialId);

        if ($qty > $effectiveAvailable) {
            $formatted = $this->formatQuantity($effectiveAvailable, $resource->unit, $resource->pieces_per_pack);
            return "Exceeds available stock ({$formatted})";
        }

        return null;
    }

    // NEW: returns the resource list for a given row, excluding resources
    // already picked in OTHER rows (so the same material can't appear twice)
    public function getResourcesForRow(int $currentIndex)
    {
        $selectedElsewhere = collect($this->materials)
            ->except($currentIndex)
            ->pluck('resource_id')
            ->filter(fn ($id) => $id !== '' && $id !== null)
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return collect($this->availableResources)
            ->reject(fn ($resource) => in_array($resource->id, $selectedElsewhere, true))
            ->values();
    }

    public function addMaterial()
    {
        $this->materials[] = ['id' => null, 'resource_id' => '', 'quantity' => 1];
    }

    public function removeMaterial(int $index)
    {
        unset($this->materials[$index]);
        $this->materials = array_values($this->materials);
    }

    // NEW: if the user picks a resource that's already used in another row,
    // block it immediately and reset that field
    public function updatedMaterials($value, $key)
    {
        if (! str_ends_with($key, '.resource_id') || $value === '' || $value === null) {
            return;
        }

        $index = (int) explode('.', $key)[0];

        $duplicateExists = collect($this->materials)
            ->except($index)
            ->pluck('resource_id')
            ->filter(fn ($id) => $id !== '' && $id !== null)
            ->map(fn ($id) => (int) $id)
            ->contains((int) $value);

        if ($duplicateExists) {
            $this->materials[$index]['resource_id'] = '';
            $this->addError("materials.$index.resource_id", 'This material is already added in another row.');
        }
    }

    public function update()
    {
        $this->validate();

        $selectedMaterials = collect($this->materials)
            ->filter(fn ($m) => !empty($m['resource_id']))
            ->values();

        // NEW: server-side duplicate guard
        $duplicateIds = $selectedMaterials
            ->pluck('resource_id')
            ->map(fn ($id) => (int) $id)
            ->duplicates();

        if ($duplicateIds->isNotEmpty()) {
            foreach ($selectedMaterials as $i => $material) {
                if ($duplicateIds->contains((int) $material['resource_id'])) {
                    $this->addError("materials.$i.resource_id", 'This material is selected more than once. Please combine the quantity into a single row instead.');
                }
            }
            return;
        }

        // Validate stock for any materials being requested
        foreach ($selectedMaterials as $i => $material) {
            $resource = Resource::find($material['resource_id']);

            if (!$resource) {
                $this->addError("materials.$i.resource_id", 'This material is no longer available.');
                return;
            }

            $alreadyHeld = ($material['id'] ?? null)
                ? RequestItem::find($material['id'])?->quantity ?? 0
                : 0;

            if ((int) $material['quantity'] > ($resource->quantity_available + $alreadyHeld)) {
                $available = $this->formatQuantity(
                    (int) $resource->quantity_available + $alreadyHeld,
                    $resource->unit,
                    $resource->pieces_per_pack
                );
                $this->addError("materials.$i.quantity", "Only {$available} of {$resource->resource_name} available.");
                return;
            }
        }

        $this->requestModel->update([
            'purpose' => $this->purpose,
        ]);

        if ($this->request_type_id == 1) {
            $facilityItem = $this->requestModel->items()->whereNull('resource_id')->first();

            $facilityData = [
                'item_name'    => $this->facility_name,
                'quantity'     => 1,
                'request_date' => $this->request_date,
                'start_time'   => $this->start_time,
                'end_time'     => $this->end_time,
            ];

            if ($facilityItem) {
                $facilityItem->update($facilityData);
            } else {
                RequestItem::create(array_merge($facilityData, [
                    'request_id'  => $this->requestModel->id,
                    'resource_id' => null,
                ]));
            }
        }

        if ($this->request_type_id == 2) {
            $this->requestModel->items()->update(['request_date' => $this->request_date]);
        }

        $keptIds = $selectedMaterials->pluck('id')->filter()->values();

        $this->requestModel->items()
            ->whereNotNull('resource_id')
            ->whereNotIn('id', $keptIds)
            ->delete();

        foreach ($selectedMaterials as $material) {
            $resource = Resource::find($material['resource_id']);

            $itemData = [
                'resource_id'  => $resource->id,
                'item_name'    => $resource->resource_name,
                'quantity'     => $material['quantity'],
                'request_date' => $this->request_date,
                'start_time'   => $this->request_type_id == 1 ? $this->start_time : null,
                'end_time'     => $this->request_type_id == 1 ? $this->end_time : null,
            ];

            if (!empty($material['id'])) {
                RequestItem::where('id', $material['id'])->update($itemData);
            } else {
                RequestItem::create(array_merge($itemData, ['request_id' => $this->requestModel->id]));
            }
        }

        session()->flash('success', 'Request updated successfully!');
        return redirect()->route('coordinator.request-to-admin.view-request');
    }

    public function cancel()
    {
        return redirect()->route('coordinator.request-to-admin.view-request');
    }
};
