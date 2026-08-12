<?php

namespace App\Livewire\Coordinator\RequestToAdmin;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\RequestType;
use App\Models\Notification;
use App\Models\User;
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
    public $start_time = '';
    public $end_time = '';

    // Material fields
    public $items = [
        ['name' => '', 'quantity' => 1],
    ];

    protected function rules()
    {
        $rules = [
            'request_type_id' => 'required|exists:request_types,id',
            'purpose'         => 'required|string|min:10|max:500',
            'request_date'    => 'required|date|after_or_equal:today',
        ];

        if ($this->request_type_id == 1) {
            $rules['facility_name'] = 'required|string|max:255';
            $rules['start_time']    = 'required';
            $rules['end_time']      = 'required|after:start_time';
        }

        if ($this->request_type_id == 2) {
            $rules['items']            = 'required|array|min:1';
            $rules['items.*.name']     = 'required|string|max:255';
            $rules['items.*.quantity'] = 'required|integer|min:1';
        }

        return $rules;
    }

    #[Computed]
    public function requestTypes()
    {
        return RequestType::all();
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

    public function updatedRequestTypeId()
    {
        $this->reset(['facility_name', 'start_time', 'end_time', 'items']);
        $this->items = [['name' => '', 'quantity' => 1]];
    }

public function submit()
{
    $this->validate();

    $user = Auth::user();

    if (!$user->department_id) {
        session()->flash('error', 'Your account has no department assigned. Please contact the admin to assign your department before submitting a request.');
        return;
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
    } else {
        foreach ($this->items as $item) {
            RequestItem::create([
                'request_id'   => $request->id,
                'resource_id'  => null,
                'item_name'    => $item['name'],
                'quantity'     => $item['quantity'],
                'request_date' => $this->request_date,
                'start_time'   => null,
                'end_time'     => null,
            ]);
        }
    }

    // ✅ FIXED: Notify all admins with correct ENUM values
    $typeName = $this->request_type_id == 1 ? 'Facility Reservation' : 'Material Request';
    $admins   = User::role('admin')->get();

    foreach ($admins as $admin) {
        Notification::create([
            'user_id' => $admin->id,                           // ✅ recipient is the admin
            'message' => $user->name . ' (Coordinator) submitted a new ' . $typeName . '.',
            'type'    => 'Gmail',                              // ✅ allowed ENUM value
            'status'  => 'pending',                            // ✅ allowed ENUM value (pending = unread)
        ]);
    }

    session()->flash('success', 'Request submitted successfully!');
    return redirect()->route('coordinator.request-to-admin.view-request');
}

    public function cancel()
    {
        return redirect()->route('coordinator.request-to-admin.view-request');
    }
};
