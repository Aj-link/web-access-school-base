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
            ->when(
                $this->search,
                fn($q) =>
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
            ->when(
                $this->search,
                fn($q) =>
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

    /**
     * Detects genuine double-bookings: same room, same active day,
     * overlapping time ranges — regardless of section/year. Two different
     * sections at the same time in different rooms is normal and NOT
     * flagged here.
     *
     * Returns [ schedule_id => Collection of the other schedules it clashes with ]
     */
    #[Computed]
    public function roomConflicts(): array
    {
        $list = $this->classesForActiveDay;
        $conflicts = [];

        foreach ($list as $a) {
            foreach ($list as $b) {
                if ($a->id === $b->id) continue;
                if ($a->room !== $b->room) continue; // different room = no conflict

                $aStart = \Carbon\Carbon::parse($a->start_time);
                $aEnd   = \Carbon\Carbon::parse($a->end_time);
                $bStart = \Carbon\Carbon::parse($b->start_time);
                $bEnd   = \Carbon\Carbon::parse($b->end_time);

                // True interval overlap — catches partial overlaps too,
                // not just identical start times.
                if ($aStart->lt($bEnd) && $bStart->lt($aEnd)) {
                    $conflicts[$a->id] ??= collect();
                    $conflicts[$a->id]->push($b);
                }
            }
        }

        return $conflicts;
    }

    /**
     * Any schedule whose time range overlaps with another's, regardless of
     * room — used purely for a neutral "these run at the same time" visual
     * cue so the UI reads as intentional, not broken. Distinct from
     * roomConflicts, which is the actual double-booking problem.
     *
     * Returns [ schedule_id => Collection of other schedules running concurrently ]
     */
    #[Computed]
    public function concurrentWith(): array
    {
        $list = $this->classesForActiveDay;
        $concurrent = [];

        foreach ($list as $a) {
            foreach ($list as $b) {
                if ($a->id === $b->id) continue;

                $aStart = \Carbon\Carbon::parse($a->start_time);
                $aEnd   = \Carbon\Carbon::parse($a->end_time);
                $bStart = \Carbon\Carbon::parse($b->start_time);
                $bEnd   = \Carbon\Carbon::parse($b->end_time);

                if ($aStart->lt($bEnd) && $bStart->lt($aEnd)) {
                    $concurrent[$a->id] ??= collect();
                    $concurrent[$a->id]->push($b);
                }
            }
        }

        return $concurrent;
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
