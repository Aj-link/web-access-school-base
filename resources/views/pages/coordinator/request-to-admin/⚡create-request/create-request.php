<?php

namespace App\Livewire\ProgramHead\RequestToAdmin;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\RequestType;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\ProgramHeadRequestNotification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
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

    protected function rules()
    {
        $rules = [
            'request_type_id' => 'required|exists:request_types,id',
            'purpose'         => 'required|string|min:10|max:500',
            'request_date'    => 'required|date|after_or_equal:today',
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
        'request_type_id.required' => 'Please select a request type',
        'purpose.required'         => 'Please state your purpose',
        'purpose.min'              => 'Purpose must be at least 10 characters',
        'request_date.required'    => 'Please select a date',
        'request_date.after_or_equal' => 'Date must be today or later',
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
    }

    #[Computed]
    public function requestTypes()
    {
        return RequestType::all();
    }

    public function getMaterialResource($resourceId)
    {
        if (!$resourceId) return null;

        return collect($this->availableResources)->firstWhere('id', (int) $resourceId);
    }

    public function getAvailableStock($resourceId): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource) {
            return null;
        }

        return $resource->available_formatted;
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

    public function getStockWarning($resourceId, $quantity): ?string
    {
        $resource = $this->getMaterialResource($resourceId);

        if (!$resource || $quantity === '' || $quantity === null) {
            return null;
        }

        $qty = (int) $quantity;

        if ($qty > (int) $resource->quantity_available) {
            return "Exceeds available stock ({$resource->available_formatted})";
        }

        return null;
    }

    public function addMaterial()
    {
        $this->materials[] = ['resource_id' => '', 'quantity' => 1];
    }

    public function removeMaterial(int $index)
    {
        unset($this->materials[$index]);
        $this->materials = array_values($this->materials);
    }

    public function updatedRequestTypeId()
    {
        $this->reset(['facility_name', 'start_time', 'end_time', 'materials']);
        $this->start_time = '09:00';
        $this->end_time   = '10:00';
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user->department_id) {
            session()->flash('error', 'Your account has no department assigned. Please contact the admin to assign your department before submitting a request.');
            return;
        }

        $selectedMaterials = collect($this->materials)
            ->filter(fn ($m) => !empty($m['resource_id']))
            ->values();

        foreach ($selectedMaterials as $i => $material) {
            $resource = Resource::find($material['resource_id']);

            if (!$resource) {
                $this->addError("materials.$i.resource_id", 'This material is no longer available.');
                return;
            }

            if ((int) $material['quantity'] > $resource->quantity_available) {
                $available = $this->formatQuantity(
                    (int) $resource->quantity_available,
                    $resource->unit,
                    $resource->pieces_per_pack
                );
                $this->addError("materials.$i.quantity", "Only {$available} of {$resource->resource_name} available.");
                return;
            }
        }

        $request = ResourceRequest::create([
            'user_id'                          => $user->id,
            'department_id'                    => $user->department_id,
            'request_type_id'                  => $this->request_type_id,
            'purpose'                          => $this->purpose,
            'status'                           => 'pending',
            'current_responsibility_center_id' => $user->responsibility_center_id ?? null,
        ]);

        if ($this->request_type_id == 1) {
            RequestItem::create([
                'request_id'   => $request->id,
                'resource_id'  => null,
                'item_name'    => $this->facility_name,
                'quantity'     => 1,
                'request_date' => $this->request_date,
                'start_time'   => $this->start_time,
                'end_time'     => $this->end_time,
            ]);
        }

        foreach ($selectedMaterials as $material) {
            $resource = Resource::find($material['resource_id']);

            RequestItem::create([
                'request_id'   => $request->id,
                'resource_id'  => $resource->id,
                'item_name'    => $resource->resource_name,
                'quantity'     => $material['quantity'],
                'request_date' => $this->request_date,
                'start_time'   => $this->request_type_id == 1 ? $this->start_time : null,
                'end_time'     => $this->request_type_id == 1 ? $this->end_time : null,
            ]);
        }

        $typeName = $this->request_type_id == 1 ? 'Facility Reservation' : 'Material Request';
        $admins   = User::role('admin')->get();

        // ✅ Build a details string for the email body
        if ($this->request_type_id == 1) {
            $materialsSummary = $selectedMaterials->isNotEmpty()
                ? ' Materials: ' . $selectedMaterials->map(function ($m) {
                    $resource = Resource::find($m['resource_id']);
                    return $resource->resource_name . ' (x' . $m['quantity'] . ')';
                })->implode(', ')
                : '';

            $details = "Facility: {$this->facility_name} on {$this->request_date} ({$this->start_time} - {$this->end_time}).{$materialsSummary}";
        } else {
            $itemsList = $selectedMaterials->map(function ($m) {
                $resource = Resource::find($m['resource_id']);
                return $resource->resource_name . ' (x' . $m['quantity'] . ')';
            })->implode(', ');

            $details = "Items: {$itemsList}";
        }

        foreach ($admins as $admin) {
            // In-app bell notification (unchanged)
            Notification::create([
                'user_id' => $admin->id,
                'message' => $user->name . ' (Program Head) submitted a new ' . $typeName . '.',
                'type'    => 'Gmail',
                'status'  => 'pending',
            ]);

            // ✅ FIX: the actual email that was missing
            $admin->notify(new ProgramHeadRequestNotification(
                $user->name,
                $typeName,
                $details
            ));
        }

        session()->flash('success', 'Request submitted successfully!');
        return redirect()->route('coordinator.request-to-admin.view-request');
    }

    public function cancel()
    {
        return redirect()->route('coordinator.request-to-admin.view-request');
    }
};
