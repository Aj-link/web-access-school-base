<?php

namespace App\Livewire\Portal;

use App\Models\ResourceUsage;
use App\Models\Resource;
use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public $reservation_id;
    public $facility_name = '';
    public $quantity = 1;
    public $used_date;
    public $purpose = '';

    protected $rules = [
        'facility_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'used_date' => 'required|date|after_or_equal:today',
        'purpose' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'facility_name.required' => 'Please enter facility name',
        'quantity.required' => 'Please enter quantity',
        'quantity.min' => 'Quantity must be at least 1',
        'used_date.required' => 'Please select a date',
        'used_date.after_or_equal' => 'Date must be today or later',
        'purpose.required' => 'Please state your purpose',
        'purpose.min' => 'Purpose must be at least 10 characters',
    ];

    public function mount($id)
    {
        $this->reservation_id = $id;
        $reservation = ResourceUsage::with('resource')->findOrFail($id);

        // Check if reservation belongs to current user
        if ($reservation->user_id !== Auth::id()) {
            session()->flash('error', 'You are not authorized to edit this reservation.');
            return redirect()->route('portal.reservations');
        }

        $this->facility_name = $reservation->resource->resource_name;
        $this->quantity = $reservation->quantity_used;
        $this->used_date = $reservation->used_date;
    }

    public function update()
    {
        $this->validate();

        $reservation = ResourceUsage::findOrFail($this->reservation_id);
        $resource = Resource::findOrFail($reservation->resource_id);

        // Calculate quantity difference
        $quantityDiff = $this->quantity - $reservation->quantity_used;

        if ($quantityDiff > 0 && $resource->quantity_available < $quantityDiff) {
            session()->flash('error', 'Not enough quantity available. Only ' . $resource->quantity_available . ' left.');
            return;
        }

        // Update resource quantity
        if ($quantityDiff != 0) {
            $resource->decrement('quantity_available', $quantityDiff);
        }

        // Update reservation
        $reservation->update([
            'quantity_used' => $this->quantity,
            'used_date' => $this->used_date,
        ]);

        // Update resource name if changed
        if ($resource->resource_name !== $this->facility_name) {
            $resource->update(['resource_name' => $this->facility_name]);
        }

        Notification::create([
            'user_id' => 1,
            'message' => 'Reservation updated by ' . Auth::user()->name . ': ' . $resource->resource_name,
            'type' => 'SMS',
            'status' => 'pending'
        ]);

        session()->flash('success', 'Reservation updated successfully!');

        return redirect()->route('portal.reservation');
    }

    public function cancel()
    {
        return redirect()->route('portal.reservation');
    }
};
