<div>
<div class="max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-neutral-200">Facility Calendar</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Room schedules and approved reservations</p>
        </div>
        <div class="flex items-center gap-3 text-xs flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                <span class="text-gray-500">Approved Reservation</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                <span class="text-gray-500">Today</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>
                <span class="text-gray-500">Class Schedule</span>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- LEFT: Calendar (now holds schedule inline) --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm overflow-hidden">

                {{-- Navigation --}}
                <div class="px-5 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                    <button wire:click="previousMonth"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 transition text-gray-600 dark:text-neutral-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                            {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                        </h3>
                        <button wire:click="goToToday"
                            class="px-2.5 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition font-medium">
                            Today
                        </button>
                    </div>

                    <button wire:click="nextMonth"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 transition text-gray-600 dark:text-neutral-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Day Headers --}}
                <div class="grid grid-cols-7 border-b border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="py-2 text-center text-[11px] font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wide">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar Grid --}}
                <div class="grid grid-cols-7 divide-x divide-y divide-gray-100 dark:divide-neutral-700">
                    @foreach($this->calendarDays as $day)
                        @php
                            $dateKey    = $day ? \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d') : null;
                            $isToday    = $dateKey === now()->format('Y-m-d');
                            $isSelected = $dateKey === $selectedDay;
                            $events     = $day ? ($this->approvedReservations[$dateKey] ?? collect()) : collect();

                            // Map this date's weekday to the schedule key
                            $weekday = $day ? \Carbon\Carbon::create($year, $month, $day)->dayOfWeek : null;
                            $dayScheduleKey = match(true) {
                                $weekday === 1 || $weekday === 3 => 'MW',
                                $weekday === 2 || $weekday === 4 => 'TTH',
                                $weekday === 5 => 'F',
                                $weekday === 6 => 'SAT',
                                default => null,
                            };
                            $daySchedule = $dayScheduleKey ? ($this->staticSchedules[$dayScheduleKey] ?? []) : [];
                            $classCount = collect($daySchedule)->flatten(1)->count();
                        @endphp

                        <div wire:click="{{ $day ? "selectDay('{$dateKey}')" : '' }}"
                            class="min-h-[92px] p-1.5 cursor-pointer transition
                                {{ $day ? 'bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700' : 'bg-gray-50 dark:bg-neutral-900' }}
                                {{ $isSelected ? 'ring-2 ring-inset ring-blue-400' : '' }}">

                            @if($day)
                                <div class="flex justify-end mb-1">
                                    <span class="w-6 h-6 flex items-center justify-center text-xs font-medium rounded-full
                                        {{ $isToday ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-neutral-400' }}">
                                        {{ $day }}
                                    </span>
                                </div>

                                {{-- Approved Reservations --}}
                                @foreach($events->take(2) as $reservation)
                                    <div class="rounded px-1 py-0.5 bg-green-100 dark:bg-green-900/30 border-l-2 border-green-500 mb-0.5">
                                        <p class="text-[9px] font-semibold text-green-800 dark:text-green-300 truncate">
                                            {{ $reservation->user->name }}
                                        </p>
                                    </div>
                                @endforeach

                                @if($events->count() > 2)
                                    <p class="text-[8px] text-gray-400 px-1">+{{ $events->count() - 2 }} more</p>
                                @endif

                                {{-- Class Schedule indicator --}}
                                @if($classCount > 0)
                                    <div class="mt-0.5 rounded px-1 py-0.5 bg-purple-100 dark:bg-purple-900/30 border-l-2 border-purple-400 flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                            <circle cx="12" cy="12" r="9"/>
                                        </svg>
                                        <p class="text-[9px] font-medium text-purple-700 dark:text-purple-300 truncate">
                                            {{ $classCount }} {{ $classCount === 1 ? 'class' : 'classes' }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- RIGHT: Selected Day Details (Reservations + Class Schedule combined) --}}
        <div class="space-y-4">
            @if($selectedDay)
                @php
                    $selectedWeekday = \Carbon\Carbon::parse($selectedDay)->dayOfWeek;
                    $selectedScheduleKey = match(true) {
                        $selectedWeekday === 1 || $selectedWeekday === 3 => 'MW',
                        $selectedWeekday === 2 || $selectedWeekday === 4 => 'TTH',
                        $selectedWeekday === 5 => 'F',
                        $selectedWeekday === 6 => 'SAT',
                        default => null,
                    };
                    $selectedDaySchedule = $selectedScheduleKey ? ($this->staticSchedules[$selectedScheduleKey] ?? []) : [];
                    $dayEvents = $this->approvedReservations[$selectedDay] ?? collect();
                @endphp

                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                            {{ \Carbon\Carbon::parse($selectedDay)->format('l, M d, Y') }}
                        </h3>
                        <p class="text-xs text-gray-400">Reservations and class schedule</p>
                    </div>

                    <div class="max-h-[640px] overflow-y-auto divide-y divide-gray-100 dark:divide-neutral-700">

                        {{-- Approved Reservations --}}
                        @if($dayEvents->isNotEmpty())
                            <div class="px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-green-600 mb-2">Approved Reservations</p>
                                @foreach($dayEvents as $reservation)
                                    @php $item = $reservation->items->first(); @endphp
                                    <div class="mb-2 rounded-lg px-3 py-2 bg-green-50 dark:bg-green-900/20 border-l-2 border-green-500">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="w-6 h-6 rounded-full bg-green-700 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                                {{ strtoupper(substr($reservation->user->name, 0, 1)) }}
                                            </div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $reservation->user->name }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                                            College of {{ $reservation->user->department->department_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                                            {{ $item->item_name ?? 'N/A' }}
                                        </p>
                                        @if($item && $item->start_time && $item->end_time)
                                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('h:i A') }} — {{ \Carbon\Carbon::parse($item->end_time)->format('h:i A') }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1 italic">{{ Str::limit($reservation->purpose, 60) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Class Schedule for this day --}}
                        @if(!empty($selectedDaySchedule))
                            <div class="px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-purple-600 mb-2">Class Schedule</p>
                                @foreach($selectedDaySchedule as $time => $classes)
                                    <div class="mb-3">
                                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                                <circle cx="12" cy="12" r="9"/>
                                            </svg>
                                            {{ $time }}
                                        </p>
                                        @foreach($classes as $class)
                                            <div class="mb-1.5 rounded-lg px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-l-2 border-purple-400">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <p class="text-xs font-semibold text-purple-800 dark:text-purple-300">
                                                            {{ $class['subject'] }}
                                                        </p>
                                                        <p class="text-[10px] text-gray-600 dark:text-neutral-400">
                                                            {{ $class['teacher'] }}
                                                        </p>
                                                        <p class="text-[10px] text-gray-500 dark:text-neutral-500">
                                                            {{ $class['section'] }} — {{ $class['dept'] }}
                                                        </p>
                                                    </div>
                                                    <span class="shrink-0 text-[9px] bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 px-1.5 py-0.5 rounded font-medium">
                                                        {{ $class['room'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($dayEvents->isEmpty() && empty($selectedDaySchedule))
                            <div class="px-4 py-8 text-center text-xs text-gray-400">
                                No reservations or classes on this day.
                            </div>
                        @endif

                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm p-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-neutral-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                    </svg>
                    <p class="text-sm text-gray-400">Select a day to see reservations and class schedule</p>
                </div>
            @endif
        </div>

    </div>

</div>
</div>
