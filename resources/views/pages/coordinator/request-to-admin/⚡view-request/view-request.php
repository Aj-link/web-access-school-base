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

    // Material fields — now resource_id-based, matching the create form
    public $items = [
        ['resource_id' => '', 'quantity' => 1],
    ];
    public $availableResources = [];

    protected function rules()
    {
        $rules = [
            'purpose'      => 'required|string|min:10|max:500',
            'request_date' => 'required|date|after_or_equal:today',
        ];

        if ($this->request_type_id == 1) {
            $rules['facility_name'] = 'required|string|max:255';
            $rules['start_time']    = 'required';
            $rules['end_time']      = 'required|after:start_time';
        }

        if ($this->request_type_id == 2) {
            $rules['items']                   = 'required|array|min:1';
            $rules['items.*.resource_id']     = 'required|exists:resources,id';
            $rules['items.*.quantity']        = 'required|integer|min:1';
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
        // ✅ FIX: load real facility + resource options, same source as the
        // create form, so editing can't drift from actual inventory.
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
            $item                = $request->items->first();
            $this->facility_name = $item?->item_name ?? '';
            $this->request_date  = $item?->request_date ?? '';
            $this->start_time    = $item?->start_time ?? '';
            $this->end_time      = $item?->end_time ?? '';
        } else {
            $this->request_date = $request->items->first()?->request_date ?? '';

            // ✅ FIX: preserve resource_id instead of discarding it
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
        $this->reset(['purpose', 'request_date', 'facility_name', 'start_time', 'end_time']);
        $this->items = [['resource_id' => '', 'quantity' => 1]];
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

        // ✅ FIX: for facility edits, ensure it's still a real, valid facility
        if ($this->request_type_id == 1 && !in_array($this->facility_name, $this->facilityOptions)) {
            $this->addError('facility_name', 'Please select a valid facility.');
            return;
        }

        // ✅ FIX: for material edits, revalidate stock exactly like the create flow
        if ($this->request_type_id == 2) {
            foreach ($this->items as $i => $item) {
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
        } else {
            foreach ($this->items as $item) {
                $resource = Resource::find($item['resource_id']);

                // ✅ FIX: resource_id is preserved, not wiped to null
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
