<?php

namespace App\Livewire\StudentFaculty;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public $purpose = '';
    public $items = [
        ['name' => '', 'quantity' => 1],
    ];

    protected $rules = [
        'purpose' => 'required|string|min:10|max:500',
        'items' => 'required|array|min:1',
        'items.*.name' => 'required|string|max:255',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    protected $messages = [
        'purpose.required' => 'Please state your purpose',
        'purpose.min' => 'Purpose must be at least 10 characters',
        'items.required' => 'Add at least one material item',
        'items.*.name.required' => 'Material name is required',
        'items.*.quantity.min' => 'Quantity must be at least 1',
    ];

    public function addItem()
    {
        $this->items[] = ['name' => '', 'quantity' => 1];
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
            RequestItem::create([
                'request_id' => $request->id,
                'resource_id' => null,
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'request_date' => now()->toDateString(),
                'start_time' => null,
                'end_time' => null,
            ]);
        }

        // Notify all coordinators
        $coordinators = User::role('coordinator')->get();
        foreach ($coordinators as $coordinator) {
            Notification::create([
                'user_id' => $coordinator->id,
                'message' => "New material request from {$user->name}: {$request->items->count()} item(s).",
                'type' => 'Gmail',
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Material request submitted! Waiting for coordinator approval.');
        return redirect()->route('portal.material');
    }

    public function cancel()
    {
        return redirect()->route('portal.material');
    }
};
