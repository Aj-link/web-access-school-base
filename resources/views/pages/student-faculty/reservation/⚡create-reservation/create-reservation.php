<?php

namespace App\Livewire\Portal;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public $facility_name = '';
    public $used_date;
    public $start_time = '09:00';
    public $end_time   = '10:00';
    public $purpose    = '';

    public array $facilityOptions    = [];
    public array $materials          = [];
    public $availableResources       = [];

    protected function rules()
    {
        return [
            'facility_name'           => 'required|string|max:255',
            'used_date'               => 'required|date|after_or_equal:today',
            'start_time'              => 'required',
            'end_time'                => 'required|after:start_time',
            'purpose'                 => 'required|string|min:10|max:500',
            'materials.*.resource_id' => 'nullable|exists:resources,id',
            'materials.*.quantity'    => 'nullable|integer|min:1',
        ];
    }

    protected $messages = [
        'facility_name.required'         => 'Please select a facility',
        'used_date.required'             => 'Please select a date',
        'used_date.after_or_equal'       => 'Date must be today or later',
        'start_time.required'            => 'Please select start time',
        'end_time.required'              => 'Please select end time',
        'end_time.after'                 => 'End time must be after start time',
        'purpose.required'               => 'Please state your purpose',
        'purpose.min'                    => 'Purpose must be at least 10 characters',
        'materials.*.resource_id.exists' => 'Selected material is invalid.',
        'materials.*.quantity.min'       => 'Quantity must be at least 1.',
    ];

    public function mount(): void
    {
        $this->used_date = date('Y-m-d');

        // ✅ Load facilities from the resources table managed by admin
        $facilityType = ResourceType::where('type_name', 'Facility')->first();

        if ($facilityType) {
            $this->facilityOptions = Resource::where('resource_type_id', $facilityType->id)
                ->where('status', 'available')
                ->orderBy('resource_name')
                ->pluck('resource_name')
                ->toArray();
        } else {
            // Fallback: any resource with "Facility" in the type name
            $this->facilityOptions = Resource::whereHas('resourceType', fn($q) =>
                $q->where('type_name', 'like', '%facility%')
                  ->orWhere('type_name', 'like', '%Facility%')
            )
            ->where('status', 'available')
            ->orderBy('resource_name')
            ->pluck('resource_name')
            ->toArray();
        }

        // ✅ FIX: Materials scoped to what's actually allocated to the user's OWN department
        // (previously pulled global quantity_available, letting any user request
        // materials never allocated to their department)
        $departmentId = Auth::user()->department_id;

        $this->availableResources = $departmentId
            ? DB::table('resource_all_locations')
                ->join('resources', 'resources.id', '=', 'resource_all_locations.resource_id')
                ->join('resource_types', 'resource_types.id', '=', 'resources.resource_type_id')
                ->where('resource_all_locations.department_id', $departmentId)
                ->where('resource_all_locations.allocated_quantity', '>', 0)
                ->where('resources.status', 'available')
                ->where('resource_types.type_name', '!=', 'Facility')
                ->select(
                    'resources.id',
                    'resources.resource_name',
                    'resource_all_locations.allocated_quantity as quantity_available'
                )
                ->orderBy('resources.resource_name')
                ->get()
            : collect();
    }

    /**
 * Materials available for a given row's dropdown: everything minus
 * whatever's already picked in OTHER material rows (prevents the same
 * resource being requested twice in one reservation). The row's own
 * current selection is always kept.
 */
public function getMaterialOptionsForRow(int $currentIndex)
{
    $selectedElsewhere = collect($this->materials)
        ->except($currentIndex)
        ->pluck('resource_id')
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->all();

    return collect($this->availableResources)
        ->reject(fn ($resource) => in_array((int) $resource->id, $selectedElsewhere))
        ->values();
}

    public function addMaterial(): void
    {
        $this->materials[] = ['resource_id' => '', 'quantity' => 1];
    }

    public function removeMaterial(int $index): void
    {
        unset($this->materials[$index]);
        $this->materials = array_values($this->materials);
    }

    public function submit()
    {
        $this->validate();

        if (is_null(Auth::user()->department_id)) {
            session()->flash('error', 'Your account is not linked to any department. Please contact the admin.');
            return;
        }

        $departmentId = Auth::user()->department_id;

        $selectedMaterials = collect($this->materials)
            ->filter(fn($m) => !empty($m['resource_id']))
            ->values();

        // ✅ FIX: Validate requested quantity against the DEPARTMENT'S allocation,
        // not the resource's global quantity_available.
        foreach ($selectedMaterials as $i => $material) {
            $allocation = DB::table('resource_all_locations')
                ->join('resources', 'resources.id', '=', 'resource_all_locations.resource_id')
                ->where('resource_all_locations.resource_id', $material['resource_id'])
                ->where('resource_all_locations.department_id', $departmentId)
                ->select('resource_all_locations.allocated_quantity', 'resources.resource_name')
                ->first();

            if (!$allocation) {
                $this->addError("materials.$i.resource_id", 'This material is not allocated to your department.');
                return;
            }

            if ((int) $material['quantity'] > $allocation->allocated_quantity) {
                $this->addError("materials.$i.quantity", "Only {$allocation->allocated_quantity} {$allocation->resource_name} available.");
                return;
            }
        }

        $request = ResourceRequest::create([
            'user_id'                          => Auth::id(),
            'department_id'                    => $departmentId,
            'request_type_id'                  => 1,
            'purpose'                          => $this->purpose,
            'status'                           => 'pending',
            'current_responsibility_center_id' => Auth::user()->responsibility_center_id,
        ]);

        RequestItem::create([
            'request_id'   => $request->id,
            'resource_id'  => null,
            'item_name'    => $this->facility_name,
            'quantity'     => 1,
            'request_date' => $this->used_date,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
        ]);

        foreach ($selectedMaterials as $material) {
            $resource = Resource::find($material['resource_id']);

            RequestItem::create([
                'request_id'   => $request->id,
                'resource_id'  => $resource->id,
                'item_name'    => $resource->resource_name,
                'quantity'     => $material['quantity'],
                'request_date' => $this->used_date,
                'start_time'   => $this->start_time,
                'end_time'     => $this->end_time,
            ]);
        }

        // Notify program heads in the same department
        $programHeads = User::role('program head')
            ->where('department_id', $departmentId)
            ->get();

        $materialsSummary = $selectedMaterials->isNotEmpty()
            ? ' with materials: ' . $selectedMaterials->map(function ($m) {
                $resource = Resource::find($m['resource_id']);
                return $resource->resource_name . ' (x' . $m['quantity'] . ')';
            })->implode(', ')
            : '';

        foreach ($programHeads as $programHead) {
            Notification::create([
                'user_id' => $programHead->id,
                'message' => Auth::user()->name . ' submitted a facility reservation for ' . $this->facility_name . ' on ' . $this->used_date . ' (' . $this->start_time . ' - ' . $this->end_time . ')' . $materialsSummary,
                'type'    => 'Gmail',
                'status'  => 'pending',
            ]);
        }

        session()->flash('success', 'Reservation submitted! Waiting for program head approval.');
        return redirect()->route('portal.reservation');
    }

    public function cancel()
    {
        return redirect()->route('portal.reservation');
    }
};
