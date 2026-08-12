<?php

namespace App\Livewire\Portal;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public $facility_name = '';
    public $used_date;
    public $start_time = '09:00';
    public $end_time = '10:00';
    public $purpose = '';
    public array $facilityOptions = [];

    protected $rules = [
        'facility_name' => 'required|string|max:255',
        'used_date'     => 'required|date|after_or_equal:today',
        'start_time'    => 'required',
        'end_time'      => 'required|after:start_time',
        'purpose'       => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'facility_name.required'   => 'Please select a facility',
        'used_date.required'       => 'Please select a date',
        'used_date.after_or_equal' => 'Date must be today or later',
        'start_time.required'      => 'Please select start time',
        'end_time.required'        => 'Please select end time',
        'end_time.after'           => 'End time must be after start time',
        'purpose.required'         => 'Please state your purpose',
        'purpose.min'              => 'Purpose must be at least 10 characters',
    ];

    public function mount()
    {
        $this->used_date = date('Y-m-d');

        $facilities = [];

        for ($floor = 1; $floor <= 4; $floor++) {
            for ($room = 1; $room <= 5; $room++) {
                $roomNumber = ($floor * 100) + $room;
                $facilities[] = 'ROOM ' . $roomNumber;
            }
        }

        for ($floor = 1; $floor <= 4; $floor++) {
            for ($room = 1; $room <= 5; $room++) {
                $roomNumber = ($floor * 100) + $room;
                $facilities[] = 'NBR ' . $roomNumber;
            }
        }

        for ($i = 1; $i <= 3; $i++) {
            $facilities[] = 'LAB ' . $i;
        }

        $facilities[] = 'LISC';

        $this->facilityOptions = $facilities;
    }

    public function submit()
    {
        $this->validate();

        if (is_null(Auth::user()->department_id)) {
            session()->flash('error', 'Your account is not linked to any department. Please contact the admin.');
            return;
        }

        $request = ResourceRequest::create([
            'user_id'                          => Auth::id(),
            'department_id'                    => Auth::user()->department_id,
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

        // Notify only coordinators from the same department
        $coordinators = User::role('coordinator')
            ->where('department_id', Auth::user()->department_id)
            ->get();

        foreach ($coordinators as $coordinator) {
            Notification::create([
                'user_id' => $coordinator->id,
                'message' => Auth::user()->name . ' submitted a facility reservation for ' . $this->facility_name . ' on ' . $this->used_date . ' (' . $this->start_time . ' - ' . $this->end_time . ')',
                'type'    => 'Gmail',
                'status'  => 'pending',
            ]);
        }

        session()->flash('success', 'Reservation submitted! Waiting for coordinator approval.');

        return redirect()->route('portal.reservation');
    }

    public function cancel()
    {
        return redirect()->route('portal.reservation');
    }
};
