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
    public $requestId;
    public $purpose = '';
    public $items = [];

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

    public function mount($id)
    {
        $this->requestId = $id;
        $request = ResourceRequest::with('items')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Only allow editing if status is pending
        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            return redirect()->route('portal.material');
        }

        $this->purpose = $request->purpose;
        $this->items = $request->items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->item_name,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    public function addItem()
    {
        $this->items[] = ['name' => '', 'quantity' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function update()
    {
        $this->validate();

        $request = ResourceRequest::where('user_id', Auth::id())
            ->where('id', $this->requestId)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update purpose
        $request->update(['purpose' => $this->purpose]);

        // Get existing item IDs
        $existingIds = collect($this->items)->filter(fn($i) => isset($i['id']))->pluck('id')->toArray();

        // Delete items that were removed
        RequestItem::where('request_id', $request->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Update or create items
        foreach ($this->items as $item) {
            if (isset($item['id'])) {
                RequestItem::where('id', $item['id'])->update([
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                ]);
            } else {
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
        }

        // Notify coordinators about the update
        $coordinators = User::role('coordinator')->get();
        foreach ($coordinators as $coordinator) {
            Notification::create([
                'user_id' => $coordinator->id,
                'message' => 'Material request #' . $request->id . ' was updated by ' . Auth::user()->name,
                'type' => 'Gmail',
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Material request updated successfully.');
        return redirect()->route('portal.material');
    }

    public function cancel()
    {
        return redirect()->route('portal.material');
    }
};
