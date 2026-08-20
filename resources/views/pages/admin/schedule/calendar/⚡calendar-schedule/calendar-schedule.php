<?php

namespace App\Livewire\Admin;

use App\Models\Schedule;
use App\Models\Request as ResourceRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public int    $year;
    public int    $month;
    public string $selectedDate = '';
    public string $activeTab    = 'schedule';

    public function mount()
    {
        $this->year         = now()->year;
        $this->month        = now()->month;
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function previousMonth()
    {
        if ($this->month === 1) {
            $this->month = 12;
            $this->year--;
        } else {
            $this->month--;
        }
    }

    public function nextMonth()
    {
        if ($this->month === 12) {
            $this->month = 1;
            $this->year++;
        } else {
            $this->month++;
        }
    }

    public function selectDate(string $date)
    {
        $this->selectedDate = $date;
    }

    #[Computed]
    public function calendarDays()
    {
        $start       = \Carbon\Carbon::create($this->year, $this->month, 1);
        $startDay    = $start->dayOfWeek;
        $daysInMonth = $start->daysInMonth;

        $days = [];

        for ($i = 0; $i < $startDay; $i++) {
            $days[] = null;
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $days[] = $d;
        }

        return $days;
    }

    #[Computed]
    public function daysWithEvents(): array
    {
        $carbon = \Carbon\Carbon::create($this->year, $this->month, 1);

        // Get all day codes that have schedules
        $schedules = Schedule::all();
        $days      = [];

        for ($d = 1; $d <= $carbon->daysInMonth; $d++) {
            $date    = \Carbon\Carbon::create($this->year, $this->month, $d);
            $dayCode = $this->getDayCode($date->dayOfWeek);

            $hasSchedule = $schedules->contains(
                fn($s) =>
                in_array($dayCode, $this->expandDayCode($s->day_type))
            );

            $hasReservation = ResourceRequest::with('items')
                ->where('request_type_id', 1)
                ->where('status', 'approved')
                ->whereHas(
                    'items',
                    fn($q) =>
                    $q->whereDate('request_date', $date->format('Y-m-d'))
                )
                ->exists();

            if ($hasSchedule || $hasReservation) {
                $days[$date->format('Y-m-d')] = [
                    'schedule'    => $hasSchedule,
                    'reservation' => $hasReservation,
                ];
            }
        }

        return $days;
    }

    #[Computed]
    public function selectedDateEvents()
    {
        if (!$this->selectedDate) return collect();

        $date    = \Carbon\Carbon::parse($this->selectedDate);
        $dayCode = $this->getDayCode($date->dayOfWeek);

        $schedules = Schedule::all()
            ->filter(fn($s) => in_array($dayCode, $this->expandDayCode($s->day_type)))
            ->sortBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('Hi'))
            ->values();

        $reservations = ResourceRequest::with(['user.department', 'items'])
            ->where('request_type_id', 1)
            ->where('status', 'approved')
            ->whereHas(
                'items',
                fn($q) =>
                $q->whereDate('request_date', $this->selectedDate)
            )
            ->get();

        return [
            'schedules'    => $schedules,
            'reservations' => $reservations,
        ];
    }

    private function getDayCode(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'SUN',
            1 => 'MON',
            2 => 'TUE',
            3 => 'WED',
            4 => 'THU',
            5 => 'FRI',
            6 => 'SAT',
            default => '',
        };
    }

    private function expandDayCode(string $code): array
    {
        return match ($code) {
            'MW'  => ['MON', 'WED'],
            'TTH' => ['TUE', 'THU'],
            'F'   => ['FRI'],
            'SAT' => ['SAT'],
            'M'   => ['MON'],
            'T'   => ['TUE'],
            'W'   => ['WED'],
            'TH'  => ['THU'],
            default => [],
        };
    }
};
