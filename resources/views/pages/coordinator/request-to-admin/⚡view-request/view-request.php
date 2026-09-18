<?php

namespace App\Livewire\Coordinator\RequestToAdmin;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\Resource;
use App\Models\ResourceType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
    // Edit modal state
    public bool $showEditModal = false;
    public ?int $editingId = null;
    public $request_type_id = '';

    // Shared fields
    public $purpose = '';
    public $request_date = '';

    // Facility fields
    public $facility_name = '';
    public $start_time = '';
    public $end_time = '';
    public array $facilityOptions = [];

    // Material fields — resource_id-based, matching the create form.
    // Used for BOTH: optional add-on to a Facility Reservation (type 1),
    // AND as the main required list for a Material Request (type 2).
    public $items = [];
    public $availableResources = [];

    protected function rules()
    {
        $rules = [
            'purpose'      => 'required|string|min:10|max:500',
            'request_date' => 'required|date|after_or_equal:today',
            'items.*.resource_id' => 'nullable|exists:resources,id',
            'items.*.quantity'    => 'nullable|integer|min:1',
        ];

        if ($this->request_type_id == 1) {
            $rules['facility_name'] = 'required|string|max:255';
            $rules['start_time']    = 'required';
            $rules['end_time']      = 'required|after:start_time';
        }

        if ($this->request_type_id == 2) {
            $rules['items']               = 'required|array|min:1';
            $rules['items.*.resource_id'] = 'required|exists:resources,id';
            $rules['items.*.quantity']    = 'required|integer|min:1';
        }

        return $rules;
    }

    protected $messages = [
        'purpose.required'         => 'Please state your purpose',
        'purpose.min'              => 'Purpose must be at least 10 characters',
        'request_date.required'    => 'Please select a date',
        'request_date.after_or_equal' => 'Date must be today or later',
        'facility_name.required'   => 'Please select a facility',
        'start_time.required'      => 'Please select start time',
        'end_time.required'        => 'Please select end time',
        'end_time.after'           => 'End time must be after start time',
        'items.required'           => 'Please add at least one material',
        'items.*.resource_id.required' => 'Please select a material',
        'items.*.resource_id.exists'   => 'Selected material is invalid.',
        'items.*.quantity.required'    => 'Please enter quantity',
        'items.*.quantity.min'         => 'Quantity must be at least 1',
    ];

    public function mount()
    {
        $facilityType = ResourceType::where('type_name', 'Facility')->first();

        $this->facilityOptions = $facilityType
            ? Resource::where('resource_type_id', $facilityType->id)
                ->where('status', 'available')
                ->orderBy('resource_name')
                ->pluck('resource_name')
                ->toArray()
            : [];

        $this->availableResources = Resource::where('status', 'available')
            ->where('quantity_available', '>', 0)
            ->where(function ($q) {
                $q->whereHas('resourceType', function ($q2) {
                    $q2->where('type_name', '!=', 'Facility')
                       ->where('type_name', 'not like', '%facility%');
                })->orWhereDoesntHave('resourceType');
            })
            ->orderBy('resource_name')
            ->get();
    }

    #[Computed]
    public function requests()
    {
        return ResourceRequest::with(['items', 'requestType'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    // Returns the resource list for a given row, excluding resources
    // already picked in OTHER rows (so the same material can't appear twice)
    public function getResourcesForRow(int $currentIndex)
    {
        $selectedElsewhere = collect($this->items)
            ->except($currentIndex)
            ->pluck('resource_id')
            ->filter(fn ($id) => $id !== '' && $id !== null)
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return collect($this->availableResources)
            ->reject(fn ($resource) => in_array($resource->id, $selectedElsewhere, true))
            ->values();
    }

    // Live guard — if a resource is picked that's already used in
    // another row, reset it and show an error
    public function updatedItems($value, $key)
    {
        if (! str_ends_with($key, '.resource_id') || $value === '' || $value === null) {
            return;
        }

        $index = (int) explode('.', $key)[0];

        $duplicateExists = collect($this->items)
            ->except($index)
            ->pluck('resource_id')
            ->filter(fn ($id) => $id !== '' && $id !== null)
            ->map(fn ($id) => (int) $id)
            ->contains((int) $value);

        if ($duplicateExists) {
            $this->items[$index]['resource_id'] = '';
            $this->addError("items.$index.resource_id", 'This material is already added in another row.');
        }
    }

    // Live re-check every time facility/date/time changes, same as Create
    public function updatedFacilityName()
    {
        $this->validateFacilityConflictLive();
    }

    public function updatedStartTime()
    {
        $this->validateFacilityConflictLive();
    }

    public function updatedEndTime()
    {
        $this->validateFacilityConflictLive();
    }

    public function updatedRequestDate()
    {
        $this->validateFacilityConflictLive();
    }

    protected function validateFacilityConflictLive(): void
    {
        $this->resetErrorBag('facility_name');

        if ($this->request_type_id == 1 && $conflictMessage = $this->facilityConflict()) {
            $this->addError('facility_name', $conflictMessage);
        }
    }

    // Checks whether the chosen facility/date/time overlaps an
    // ALREADY APPROVED reservation for the same facility — excluding
    // this request's own current facility item, since editing your
    // own booking shouldn't conflict with itself.
    protected function facilityConflict(): ?string
    {
        if (!$this->facility_name || !$this->request_date || !$this->start_time || !$this->end_time) {
            return null;
        }

        $conflict = RequestItem::whereNull('resource_id')
            ->where('item_name', $this->facility_name)
            ->where('request_date', $this->request_date)
            ->where('request_id', '!=', $this->editingId)
            ->whereHas('request', fn ($q) => $q->where('status', 'approved'))
            ->where('start_time', '<', $this->end_time)
            ->where('end_time', '>', $this->start_time)
            ->first();

        if ($conflict) {
            $from = \Carbon\Carbon::parse($conflict->start_time)->format('h:i A');
            $to   = \Carbon\Carbon::parse($conflict->end_time)->format('h:i A');

            return "Sorry, {$this->facility_name} is already booked on "
                . \Carbon\Carbon::parse($this->request_date)->format('M d, Y')
                . " from {$from} to {$to}. Please choose a different time or date.";
        }

        return null;
    }

    public function openEdit(int $id)
    {
        $request = ResourceRequest::with('items')->findOrFail($id);

        if ($request->user_id !== Auth::id()) {
            session()->flash('error', 'You are not authorized to edit this request.');
            return;
        }

        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            return;
        }

        $this->editingId       = $id;
        $this->request_type_id = $request->request_type_id;
        $this->purpose         = $request->purpose;

        if ($request->request_type_id == 1) {
            $facilityItem = $request->items->whereNull('resource_id')->first();

            $this->facility_name = $facilityItem?->item_name ?? '';
            $this->request_date  = $facilityItem?->request_date ?? '';
            $this->start_time    = $facilityItem?->start_time ?? '';
            $this->end_time      = $facilityItem?->end_time ?? '';

            // NEW: load any materials already attached to this facility reservation
            $this->items = $request->items
                ->whereNotNull('resource_id')
                ->map(fn ($i) => [
                    'resource_id' => $i->resource_id,
                    'quantity'    => $i->quantity,
                ])
                ->values()
                ->toArray();
        } else {
            $this->request_date = $request->items->first()?->request_date ?? '';

            $this->items = $request->items->map(fn($i) => [
                'resource_id' => $i->resource_id,
                'quantity'    => $i->quantity,
            ])->toArray();

            if (empty($this->items)) {
                $this->items = [['resource_id' => '', 'quantity' => 1]];
            }
        }

        $this->showEditModal = true;
    }

    public function closeEdit()
    {
        $this->showEditModal   = false;
        $this->editingId       = null;
        $this->request_type_id = '';
        $this->resetErrorBag();
        $this->reset(['purpose', 'request_date', 'facility_name', 'start_time', 'end_time']);
        $this->items = [];
    }

    public function saveEdit()
    {
        $this->validate();

        $request = ResourceRequest::where('user_id', Auth::id())->findOrFail($this->editingId);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request can no longer be edited.');
            $this->closeEdit();
            return;
        }

        if ($this->request_type_id == 1 && !in_array($this->facility_name, $this->facilityOptions)) {
            $this->addError('facility_name', 'Please select a valid facility.');
            return;
        }

        if ($this->request_type_id == 1) {
            if ($conflictMessage = $this->facilityConflict()) {
                $this->addError('facility_name', $conflictMessage);
                return;
            }
        }

        // Materials to actually save — applies to BOTH facility (optional)
        // and material-request (required) rows, mirroring the Create form
        $selectedItems = collect($this->items)
            ->filter(fn ($i) => !empty($i['resource_id']))
            ->values();

        // Duplicate guard — applies whenever more than one material row is picked
        $duplicateIds = $selectedItems
            ->pluck('resource_id')
            ->map(fn ($id) => (int) $id)
            ->duplicates();

        if ($duplicateIds->isNotEmpty()) {
            foreach ($selectedItems as $i => $item) {
                if ($duplicateIds->contains((int) $item['resource_id'])) {
                    $this->addError("items.$i.resource_id", 'This material is selected more than once. Please combine the quantity into a single row instead.');
                }
            }
            return;
        }

        foreach ($selectedItems as $i => $item) {
            $resource = Resource::find($item['resource_id']);

            if (!$resource) {
                $this->addError("items.$i.resource_id", 'This material is no longer available.');
                return;
            }

            if ((int) $item['quantity'] > $resource->quantity_available) {
                $this->addError("items.$i.quantity", "Only {$resource->quantity_available} of {$resource->resource_name} available.");
                return;
            }
        }

        $request->update(['purpose' => $this->purpose]);

        $request->items()->delete();

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

        foreach ($selectedItems as $item) {
            $resource = Resource::find($item['resource_id']);

            RequestItem::create([
                'request_id'   => $request->id,
                'resource_id'  => $resource->id,
                'item_name'    => $resource->resource_name,
                'quantity'     => $item['quantity'],
                'request_date' => $this->request_date,
                'start_time'   => null,
                'end_time'     => null,
            ]);
        }

        $this->closeEdit();
        session()->flash('success', 'Request updated successfully!');
    }

    public function addItem()
    {
        $this->items[] = ['resource_id' => '', 'quantity' => 1];
    }

    public function removeItem(int $index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function delete(int $id)
    {
        $request = ResourceRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be deleted.');
            return;
        }

        $request->delete();
        session()->flash('success', 'Request deleted.');
    }
};
