<div>
<div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="flex flex-col gap-6">

        {{-- Flash --}}
        @if(session()->has('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Class Schedules</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">1st Semester AY 2026-2027</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <input type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Search subject, teacher, room..."
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-green-500 w-64">

                <a href="{{ route('admin.schedule.create') }}"
                    class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    + Add Schedule
                </a>
            </div>
        </div>

        {{-- Day Tabs --}}
        <div class="flex gap-2 flex-wrap">
            @foreach($this->days as $key => $label)
                <button wire:click="selectDay('{{ $key }}')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2
                        {{ $activeDay === $key
                            ? 'bg-green-600 text-white shadow'
                            : 'bg-white dark:bg-neutral-800 text-gray-600 dark:text-neutral-300 border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-700' }}">
                    {{ $label }}
                    <span class="text-xs px-1.5 py-0.5 rounded-full
                        {{ $activeDay === $key ? 'bg-white/20' : 'bg-gray-100 dark:bg-neutral-700' }}">
                        {{ $this->countsByDay[$key] }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Class List for Active Day --}}
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                    {{ $this->days[$activeDay] }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    {{ $this->classesForActiveDay->count() }} {{ $this->classesForActiveDay->count() === 1 ? 'class' : 'classes' }}
                </p>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-neutral-700">
                @forelse($this->classesForActiveDay as $schedule)
                    <div class="group flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-neutral-700/40 transition">

                        {{-- Time column --}}
                        <div class="w-28 shrink-0 text-sm font-semibold text-gray-700 dark:text-neutral-300">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                            <div class="text-xs font-normal text-gray-400">
                                – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold text-gray-800 dark:text-neutral-200">
                                    {{ $schedule->subject_code }}
                                </p>
                                <span class="text-xs px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full font-medium">
                                    {{ $schedule->section }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">
                                {{ $schedule->teacher }} · {{ $schedule->department }}
                            </p>
                        </div>

                        {{-- Room --}}
                        <div class="shrink-0">
                            <span class="text-xs px-2.5 py-1 bg-gray-100 dark:bg-neutral-700 text-gray-700 dark:text-neutral-300 rounded-lg font-medium">
                                {{ $schedule->room }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="shrink-0 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.schedule.edit', $schedule->id) }}"
                                class="px-2.5 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Edit
                            </a>
                            <button wire:click="delete({{ $schedule->id }})"
                                wire:confirm="Delete this schedule?"
                                class="px-2.5 py-1.5 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center text-gray-400">
                        No classes scheduled on {{ $this->days[$activeDay] }}.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
</div>
