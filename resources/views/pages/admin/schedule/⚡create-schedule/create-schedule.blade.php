<div class="select-none">
<div class="max-w-3xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Add Schedule</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">Create a new class schedule</p>
            </div>
            <a href="{{ route('admin.schedule') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 transition">
                ← Back
            </a>
        </div>

        <div class="p-6">

            @if(session()->has('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-5">

                {{-- Subject Code + Name --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Subject Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="subject_code"
                            placeholder="e.g. GECC 113"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('subject_code')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Subject Name
                        </label>
                        <input type="text" wire:model="subject_name"
                            placeholder="e.g. General Education"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('subject_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Teacher --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Teacher <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="teacher"
                        placeholder="e.g. DR. EFREN"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('teacher')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section + Department --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Section <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="section"
                            placeholder="e.g. BPED1A"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('section')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="department"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                        @error('department')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Year Level --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Year Level <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="year_level"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Select Year Level --</option>
                        <option value="First Year">First Year</option>
                        <option value="Second Year">Second Year</option>
                        <option value="Third Year">Third Year</option>
                        <option value="Fourth Year">Fourth Year</option>
                    </select>
                    @error('year_level')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Day Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Day <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="day_type"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Select Day --</option>
                        <option value="MW">Monday & Wednesday</option>
                        <option value="TTH">Tuesday & Thursday</option>
                        <option value="F">Friday</option>
                        <option value="SAT">Saturday</option>
                        <option value="M">Monday only</option>
                        <option value="T">Tuesday only</option>
                        <option value="W">Wednesday only</option>
                        <option value="TH">Thursday only</option>
                    </select>
                    @error('day_type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Start Time + End Time --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" wire:model="start_time"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('start_time')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" wire:model="end_time"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('end_time')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Room --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Room <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="room"
                        placeholder="e.g. RM 207, LAB 1, IS FIELD"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('room')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Year + Semester --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            School Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="school_year"
                            placeholder="e.g. 2026-2027"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('school_year')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="semester"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                        @error('semester')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition">
                        Save Schedule
                    </button>
                    <button type="button" wire:click="cancel"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium text-sm transition">
                        Cancel
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</div>
