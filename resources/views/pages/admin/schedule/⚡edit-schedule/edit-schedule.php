<?php

namespace App\Livewire\Admin\Schedule;

use App\Models\Resource;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public Resource $resource;

    public string $facility_name = '';
    public string $status        = 'available';

    protected array $rules = [
        'facility_name' => 'required|string|max:255',
        'status'        => 'required|in:available,maintenance',
    ];

    protected array $messages = [
        'facility_name.required' => 'Please enter the facility name',
        'status.required'        => 'Please select a status',
    ];

    public function mount(int $id)
    {
        $this->resource = Resource::findOrFail($id);

        $this->facility_name = $this->resource->resource_name ?? '';
        $this->status         = $this->resource->status ?? 'available';
    }

    public function submit()
    {
        $this->validate();

        $this->resource->update([
            'resource_name' => $this->facility_name,
            'status'        => $this->status,
        ]);

        session()->flash('success', 'Facility updated successfully.');
        return redirect()->route('admin.schedule');
    }

    public function cancel()
    {
        return redirect()->route('admin.schedule');
    }
};
