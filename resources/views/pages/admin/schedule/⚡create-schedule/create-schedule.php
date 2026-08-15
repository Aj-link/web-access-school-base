<?php

namespace App\Livewire\Admin\Schedule;

use App\Models\Department;
use App\Models\Schedule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public string $subject_code  = '';
    public string $subject_name  = '';
    public string $teacher       = '';
    public string $section       = '';
    public string $department    = '';
    public string $year_level    = '';
    public string $day_type      = '';
    public string $start_time    = '';
    public string $end_time      = '';
    public string $room          = '';
    public string $school_year   = '2026-2027';
    public string $semester      = '1st Semester';

    protected array $rules = [
        'subject_code' => 'required|string|max:255',
        'subject_name' => 'nullable|string|max:255',
        'teacher'      => 'required|string|max:255',
        'section'      => 'required|string|max:255',
        'department'   => 'required|string|max:255',
        'year_level'   => 'required|string|max:255',
        'day_type'     => 'required|string',
        'start_time'   => 'required',
        'end_time'     => 'required|after:start_time',
        'room'         => 'required|string|max:255',
        'school_year'  => 'required|string|max:255',
        'semester'     => 'required|string|max:255',
    ];

    public function submit()
    {
        $this->validate();

        Schedule::create([
            'subject_code' => $this->subject_code,
            'subject_name' => $this->subject_name,
            'teacher'      => $this->teacher,
            'section'      => $this->section,
            'department'   => $this->department,
            'year_level'   => $this->year_level,
            'day_type'     => $this->day_type,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
            'room'         => $this->room,
            'school_year'  => $this->school_year,
            'semester'     => $this->semester,
        ]);

        session()->flash('success', 'Schedule created successfully.');
        return redirect()->route('admin.schedule');
    }

    public function cancel()
    {
        return redirect()->route('admin.schedule');
    }

    public function with(): array
    {
        return [
            'departments' => Department::orderBy('department_name')->get(),
        ];
    }
};
