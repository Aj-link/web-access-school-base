<?php

namespace App\Livewire\Admin\Schedule;

use App\Models\Schedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public string $search = '';
    public string $activeDay = 'MON';

    public function updatingSearch() {}

    public function selectDay(string $day)
    {
        $this->activeDay = $day;
    }

    public function delete(int $id)
    {
        Schedule::findOrFail($id)->delete();
        session()->flash('success', 'Schedule deleted successfully.');
    }

    #[Computed]
    public function days(): array
    {
        return [
            'MON' => 'Monday',
            'TUE' => 'Tuesday',
            'WED' => 'Wednesday',
            'THU' => 'Thursday',
            'FRI' => 'Friday',
            'SAT' => 'Saturday',
        ];
    }

    /**
     * Every schedule whose day_type expands to include the active day,
     * matching the search filter, sorted by start time. Simple flat list —
     * no grid, no rowspan, nothing to misread.
     */
    #[Computed]
    public function classesForActiveDay()
    {
        return Schedule::query()
            ->when($this->search, fn($q) =>
                $q->where('subject_code', 'like', '%' . $this->search . '%')
                  ->orWhere('teacher', 'like', '%' . $this->search . '%')
                  ->orWhere('section', 'like', '%' . $this->search . '%')
                  ->orWhere('room', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%')
            )
            ->get()
            ->filter(fn($schedule) => in_array($this->activeDay, $this->expandDayCode($schedule->day_type)))
            ->sortBy(fn($schedule) => \Carbon\Carbon::parse($schedule->start_time)->format('Hi'))
            ->values();
    }

    /**
     * Count per day, shown as a small badge on each tab — helps someone
     * see at a glance which days are busy without clicking through.
     */
    #[Computed]
    public function countsByDay(): array
    {
        $all = Schedule::query()
            ->when($this->search, fn($q) =>
                $q->where('subject_code', 'like', '%' . $this->search . '%')
                  ->orWhere('teacher', 'like', '%' . $this->search . '%')
                  ->orWhere('section', 'like', '%' . $this->search . '%')
                  ->orWhere('room', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%')
            )
            ->get();

        $counts = array_fill_keys(array_keys($this->days), 0);

        foreach ($all as $schedule) {
            foreach ($this->expandDayCode($schedule->day_type) as $day) {
                if (isset($counts[$day])) $counts[$day]++;
            }
        }

        return $counts;
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
