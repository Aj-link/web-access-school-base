<div>
<div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Edit Reservation</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Update your facility reservation</p>
        </div>

        <div class="p-6">
            @if (session()->has('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit="update" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Facility Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        wire:model="facility_name"
                        placeholder="Enter facility name"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                    @error('facility_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                        wire:model="quantity"
                        min="1"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                    @error('quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Reservation Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                        wire:model="used_date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                    @error('used_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        Update Reservation
                    </button>
                    <button type="button"
                        wire:click="cancel"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
