<div class="select-none">
<div class="max-w-2xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Create Department</h2>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Add a new department to the system</p>
            </div>
            <a href="{{ route('admin.departments') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back
            </a>
        </div>

        <div class="p-6">

            {{-- Flash Messages --}}
            @if(session()->has('success'))
                <div class="mb-5 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session()->has('error'))
                <div class="mb-5 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit="submit" class="space-y-5">

                {{-- Department Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        wire:model="department_name"
                        placeholder="e.g. Computer Studies, Engineering, Business"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('department_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition">
                        <span wire:loading.remove wire:target="submit">Create Department</span>
                        <span wire:loading wire:target="submit">Creating...</span>
                    </button>
                    <button type="button"
                        wire:click="cancel"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium text-sm transition">
                        Cancel
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</div>
