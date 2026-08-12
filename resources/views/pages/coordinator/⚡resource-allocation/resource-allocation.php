<?php

namespace App\Livewire\Coordinator;

use App\Models\Resource;
use App\Models\Department;
use App\Models\ResourceAllLocation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.coordinator')] class extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editId = null;
    public $resource_id = '';
    public $department_id = '';
    public $allocated_quantity = 1;

    protected $rules = [
        'resource_id' => 'required|exists:resources,id',
        'department_id' => 'required|exists:departments,id',
        'allocated_quantity' => 'required|integer|min:1',
    ];

    protected $messages = [
        'resource_id.required' => 'Please select a resource',
        'department_id.required' => 'Please select a department',
        'allocated_quantity.required' => 'Please enter quantity',
        'allocated_quantity.min' => 'Quantity must be at least 1',
    ];

    public function openCreateModal()
    {
        $this->reset(['editId', 'resource_id', 'department_id', 'allocated_quantity']);
        $this->allocated_quantity = 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $allocation = ResourceAllLocation::findOrFail($id);
        $this->editId = $allocation->id;
        $this->resource_id = $allocation->resource_id;
        $this->department_id = $allocation->department_id;
        $this->allocated_quantity = $allocation->allocated_quantity;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $allocation = ResourceAllLocation::findOrFail($this->editId);
            $allocation->update([
                'resource_id' => $this->resource_id,
                'department_id' => $this->department_id,
                'allocated_quantity' => $this->allocated_quantity,
            ]);
            session()->flash('message', 'Allocation updated successfully.');
        } else {
            // Check if allocation already exists for this resource+department
            $exists = ResourceAllLocation::where('resource_id', $this->resource_id)
                ->where('department_id', $this->department_id)
                ->exists();
            if ($exists) {
                session()->flash('error', 'This resource is already allocated to this department.');
                return;
            }

            ResourceAllLocation::create([
                'resource_id' => $this->resource_id,
                'department_id' => $this->department_id,
                'allocated_quantity' => $this->allocated_quantity,
            ]);
            session()->flash('message', 'Allocation created successfully.');
        }

        $this->showModal = false;
        $this->reset(['editId', 'resource_id', 'department_id', 'allocated_quantity']);
    }

    public function delete($id)
    {
        ResourceAllLocation::findOrFail($id)->delete();
        session()->flash('message', 'Allocation deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editId', 'resource_id', 'department_id', 'allocated_quantity']);
    }

    #[Computed]
    public function allocations()
    {
        return ResourceAllLocation::with(['resource', 'department'])
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function resources()
    {
        return Resource::where('status', 'available')->get();
    }

    #[Computed]
    public function departments()
    {
        return Department::all();
    }
};
