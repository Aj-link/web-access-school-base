<div class="select-none">
<div class="max-w-[85rem] px-4 py-8 sm:px-6 lg:px-8 mx-auto space-y-5">

    {{-- Flash --}}
    @if(session()->has('success'))
        <div class="p-3 bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 rounded-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Class Schedules</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400">1st Semester, AY 2026–2027</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" wire:model.live="search"
                    placeholder="Search subject, teacher, room..."
                    class="pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
            </div>
            <a href="{{ route('admin.schedule.create') }}"
                class="px-3.5 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Add Schedule
            </a>
        </div>
    </div>

    {{-- Room-conflict summary banner --}}
    @if(count($this->roomConflicts) > 0)
        <div class="p-3 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span class="font-medium">{{ count($this->roomConflicts) }}</span>
            schedule{{ count($this->roomConflicts) > 1 ? 's have' : ' has' }} a room double-booking on {{ $this->days[$activeDay] }} — flagged below.
        </div>
    @endif

    {{-- Day Tabs + Table --}}
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">

        {{-- Day Tabs --}}
        <div class="flex border-b border-gray-200 dark:border-neutral-700 px-2 overflow-x-auto">
            @foreach($this->days as $code => $label)
                <button wire:click="selectDay('{{ $code }}')"
                    class="px-4 py-3 text-sm font-medium transition relative flex items-center gap-2 shrink-0
                        {{ $activeDay === $code
                            ? 'text-blue-600 dark:text-blue-400'
                            : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200' }}">
                    {{ $label }}
                    @if($this->countsByDay[$code] > 0)
                        <span class="text-[11px] min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full font-semibold
                            {{ $activeDay === $code
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                : 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400' }}">
                            {{ $this->countsByDay[$code] }}
                        </span>
                    @endif
                    @if($activeDay === $code)
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] bg-blue-600 dark:bg-blue-400"></span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Table header (desktop only) --}}
        @if($this->classesForActiveDay->isNotEmpty())
            <div class="hidden md:grid grid-cols-[130px_1fr_140px_160px_110px_80px] gap-3 px-4 py-2 bg-gray-50 dark:bg-neutral-900/40 border-b border-gray-200 dark:border-neutral-700 text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-neutral-500">
                <span>Time</span>
                <span>Subject</span>
                <span>Teacher</span>
                <span>Room</span>
                <span>Section / Year</span>
                <span class="text-right">Actions</span>
            </div>
        @endif

        {{-- Rows --}}
        <div class="divide-y divide-gray-100 dark:divide-neutral-700">
            @forelse($this->classesForActiveDay as $schedule)
                @php
                    $roomConflict = $this->roomConflicts[$schedule->id] ?? null;
                    $concurrent = $this->concurrentWith[$schedule->id] ?? null;
                    // Only show the neutral "same time" note when it's NOT already
                    // a room conflict — the red banner covers that case.
                    $showConcurrentNote = $concurrent && !$roomConflict;
                @endphp

                <div class="{{ $roomConflict ? 'bg-red-50/60 dark:bg-red-900/10' : '' }}">

                    @if($roomConflict)
                        <div class="px-4 pt-2.5 flex items-start gap-1.5 text-[11px] font-semibold text-red-600 dark:text-red-400">
                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            <span>
                                Room {{ $schedule->room }} is also booked for
                                {{ $roomConflict->map(fn($c) => $c->subject_code . ' (' . $c->section . ')')->join(', ') }}
                                at
                                {{ $roomConflict->map(fn($c) => \Carbon\Carbon::parse($c->start_time)->format('h:i A') . '–' . \Carbon\Carbon::parse($c->end_time)->format('h:i A'))->unique()->join(', ') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex flex-col md:grid md:grid-cols-[130px_1fr_140px_160px_110px_80px] gap-1 md:gap-3 md:items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition">

                        {{-- Time --}}
                        <div>
                            <div class="text-sm text-gray-700 dark:text-neutral-300 font-medium tabular-nums">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                <span class="text-gray-400">–</span>
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </div>
                            @if($showConcurrentNote)
                                <div class="flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                                    <span class="text-[10px] text-slate-500 dark:text-neutral-400">
                                        Also running: {{ $concurrent->pluck('room')->unique()->join(', ') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Subject --}}
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-neutral-200 truncate">
                                {{ $schedule->subject_code }}
                                @if($schedule->subject_name)
                                    <span class="font-normal text-gray-400"> — {{ $schedule->subject_name }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Teacher --}}
                        <div class="text-sm text-gray-600 dark:text-neutral-400 truncate">
                            {{ $schedule->teacher }}
                        </div>

                        {{-- Room / Dept --}}
                        <div class="text-sm truncate flex items-center gap-1.5">
                            <span class="{{ $roomConflict ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-600 dark:text-neutral-400' }}">
                                {{ $schedule->room }}
                            </span>
                            @if($roomConflict)
                                <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                            @endif
                            <span class="text-gray-400">· {{ $schedule->department }}</span>
                        </div>

                        {{-- Section / Year --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 text-[11px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded-md">
                                {{ $schedule->section }}
                            </span>
                            <span class="px-2 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-300 rounded-md">
                                {{ $schedule->year_level }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 md:justify-end mt-1 md:mt-0">
                            <a href="{{ route('admin.schedule.edit', $schedule->id) }}"
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button wire:click="delete({{ $schedule->id }})"
                                wire:confirm="Delete this schedule?"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <div class="py-14 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-neutral-700 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-neutral-400">
                        No classes on {{ $this->days[$activeDay] }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">
                        Try a different day or add a schedule
                    </p>
                    <a href="{{ route('admin.schedule.create') }}"
                        class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        Add Schedule
                    </a>
                </div>
            @endforelse
        </div>

    </div>

</div>
</div>
