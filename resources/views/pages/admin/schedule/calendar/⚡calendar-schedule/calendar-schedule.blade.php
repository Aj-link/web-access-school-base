<div class="select-none">
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Calendar --}}
        <div class="w-full lg:w-80 shrink-0">
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-700 overflow-hidden">

                {{-- Calendar Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                    <button wire:click="previousMonth"
                        class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-700 transition text-gray-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-neutral-200 tracking-wide">
                        {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                    </h2>
                    <button wire:click="nextMonth"
                        class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-700 transition text-gray-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-gray-100 dark:border-neutral-700">
                    <button wire:click="$set('activeTab', 'schedule')"
                        class="flex-1 py-2.5 text-xs font-semibold transition border-b-2
                            {{ $activeTab === 'schedule'
                                ? 'border-gray-800 text-gray-800 dark:border-neutral-200 dark:text-neutral-200'
                                : 'border-transparent text-gray-400 dark:text-neutral-500 hover:text-gray-600' }}">
                        SCHEDULE
                    </button>
                    <button wire:click="$set('activeTab', 'reservation')"
                        class="flex-1 py-2.5 text-xs font-semibold transition border-b-2
                            {{ $activeTab === 'reservation'
                                ? 'border-gray-800 text-gray-800 dark:border-neutral-200 dark:text-neutral-200'
                                : 'border-transparent text-gray-400 dark:text-neutral-500 hover:text-gray-600' }}">
                        RESERVATIONS
                    </button>
                </div>

                {{-- Day Headers --}}
                <div class="grid grid-cols-7 px-3 pt-3">
                    @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $d)
                        <div class="text-center text-[10px] font-semibold text-gray-400 dark:text-neutral-500 py-1">
                            {{ $d }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar Days --}}
                <div class="grid grid-cols-7 px-3 pb-4 gap-y-0.5">
                    @foreach($this->calendarDays as $day)
                        @php
                            $dateKey    = $day ? \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d') : null;
                            $isToday    = $dateKey === now()->format('Y-m-d');
                            $isSelected = $dateKey === $selectedDate;
                            $events     = $day ? ($this->daysWithEvents[$dateKey] ?? null) : null;
                        @endphp

                        <div class="flex flex-col items-center py-0.5">
                            @if($day)
                                <button wire:click="selectDate('{{ $dateKey }}')"
                                    class="relative w-8 h-8 flex items-center justify-center text-sm rounded-full transition font-medium
                                        {{ $isSelected
                                            ? 'bg-teal-500 text-white'
                                            : ($isToday
                                                ? 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300'
                                                : 'text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700') }}">
                                    {{ $day }}
                                </button>
                                {{-- Event dots --}}
                                @if($events)
                                    <div class="flex gap-0.5 mt-0.5">
                                        @if($events['schedule'])
                                            <span class="size-1 rounded-full bg-blue-500 inline-block"></span>
                                        @endif
                                        @if($events['reservation'])
                                            <span class="size-1 rounded-full bg-green-500 inline-block"></span>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="px-4 pb-4 flex items-center gap-4 text-[10px] text-gray-400 dark:text-neutral-500">
                    <div class="flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-blue-500 inline-block"></span>
                        Schedule
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-green-500 inline-block"></span>
                        Reservation
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT: Events Panel --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-700 overflow-hidden h-full">

                {{-- Panel Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-neutral-700">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest">DATE</p>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <h3 class="text-4xl font-bold text-gray-800 dark:text-neutral-200">
                                {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('d') : '--' }}
                            </h3>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l') : '' }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">
                                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('F Y') : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest">EVENTS</p>
                </div>

                {{-- Events List --}}
                <div class="overflow-y-auto max-h-[600px] divide-y divide-gray-50 dark:divide-neutral-700">

                    @php
                        $events = $this->selectedDateEvents;
                        $schedules    = $events['schedules'] ?? collect();
                        $reservations = $events['reservations'] ?? collect();
                    @endphp

                    {{-- Schedules --}}
                    @if($activeTab === 'schedule' || $activeTab === 'all')
                        @forelse($schedules as $schedule)
                            <div class="flex items-stretch gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-neutral-700 transition">

                                {{-- Timeline dot --}}
                                <div class="flex flex-col items-center">
                                    <div class="size-2.5 rounded-full bg-gray-200 dark:bg-neutral-600 mt-1.5 shrink-0"></div>
                                    <div class="w-px flex-1 bg-gray-100 dark:bg-neutral-700 mt-1"></div>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0 pb-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <span class="inline-flex items-center py-0.5 px-2 text-[10px] font-semibold bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                                    CLASS
                                                </span>
                                                <span class="inline-flex items-center py-0.5 px-2 text-[10px] font-medium bg-gray-100 text-gray-600 rounded-full dark:bg-neutral-700 dark:text-neutral-300">
                                                    {{ $schedule->section }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-neutral-200 truncate">
                                                {{ $schedule->subject_code }}
                                                @if($schedule->subject_name)
                                                    <span class="font-normal text-gray-400 dark:text-neutral-500"> — {{ $schedule->subject_name }}</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                                                👤 {{ $schedule->teacher }} &nbsp;·&nbsp; 🏢 {{ $schedule->department }}
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                            </p>
                                            <span class="inline-block mt-1 py-0.5 px-2 text-[10px] bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 rounded font-medium">
                                                📍 {{ $schedule->room }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @empty
                            @if($activeTab === 'schedule')
                                <div class="px-6 py-10 text-center">
                                    <p class="text-sm text-gray-400 dark:text-neutral-500">No classes scheduled on this day.</p>
                                </div>
                            @endif
                        @endforelse
                    @endif

                    {{-- Reservations --}}
                    @if($activeTab === 'reservation' || $activeTab === 'all')
                        @forelse($reservations as $reservation)
                            @php $item = $reservation->items->first(); @endphp
                            <div class="flex items-stretch gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-neutral-700 transition">

                                {{-- Timeline dot --}}
                                <div class="flex flex-col items-center">
                                    <div class="size-2.5 rounded-full bg-green-400 dark:bg-green-600 mt-1.5 shrink-0 ring-4 ring-green-50 dark:ring-green-900"></div>
                                    <div class="w-px flex-1 bg-gray-100 dark:bg-neutral-700 mt-1"></div>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0 pb-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <span class="inline-flex items-center py-0.5 px-2 text-[10px] font-semibold bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">
                                                    RESERVATION
                                                </span>
                                                <span class="inline-flex items-center py-0.5 px-2 text-[10px] font-medium bg-gray-100 text-gray-600 rounded-full dark:bg-neutral-700 dark:text-neutral-300">
                                                    {{ $reservation->user->department->department_name ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-neutral-200 truncate">
                                                {{ $item->item_name ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                                                👤 {{ $reservation->user->name }}
                                            </p>
                                            @if($reservation->purpose)
                                                <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5 italic">
                                                    "{{ Str::limit($reservation->purpose, 60) }}"
                                                </p>
                                            @endif
                                        </div>
                                        @if($item && $item->start_time && $item->end_time)
                                            <div class="shrink-0 text-right">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400">
                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('g:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('g:i A') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @empty
                            @if($activeTab === 'reservation')
                                <div class="px-6 py-10 text-center">
                                    <p class="text-sm text-gray-400 dark:text-neutral-500">No reservations on this day.</p>
                                </div>
                            @endif
                        @endforelse
                    @endif

                    {{-- Both empty --}}
                    @if($schedules->isEmpty() && $reservations->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                            <div class="size-14 rounded-full bg-gray-100 dark:bg-neutral-700 flex items-center justify-center mb-3">
                                <svg class="size-7 text-gray-300 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Nothing on this day</p>
                            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Select another date to see events</p>
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

</div>
</div>
