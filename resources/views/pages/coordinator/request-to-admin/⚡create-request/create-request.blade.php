<div class="select-none">
<div class="max-w-3xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Create Request to Admin</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Submit a facility reservation or material request</p>
        </div>

        <div class="p-6">

            {{-- ✅ Success Flash --}}
            @if(session()->has('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ Error Flash --}}
            @if(session()->has('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ✅ Livewire 4 uses wire:submit not wire:submit.prevent --}}
            <form wire:submit="submit" class="space-y-6">

                {{-- Request Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Request Type <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="request_type_id"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                        <option value="">-- Select Request Type --</option>
                        @foreach($this->requestTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                    @error('request_type_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shared: Purpose --}}
                @if($request_type_id)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="purpose" rows="3"
                        placeholder="State your purpose..."
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200"></textarea>
                    @error('purpose')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shared: Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="request_date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                    @error('request_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                {{-- Facility Fields --}}
                @if($request_type_id == 1)
                <div class="space-y-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700">
                    <p class="text-sm font-semibold text-green-700 dark:text-green-300">Facility Details</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Facility Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="facility_name"
                            placeholder="e.g. Room 101, Computer Lab, Gymnasium"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                        @error('facility_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="start_time"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                            @error('start_time')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="end_time"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                            @error('end_time')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                {{-- Material Fields --}}
                @if($request_type_id == 2)
                <div class="space-y-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700">
                    <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Materials Needed</p>

                    <div class="space-y-3">
                        @foreach($items as $index => $item)
                            <div class="flex gap-3 items-center">
                                <input type="text"
                                    wire:model="items.{{ $index }}.name"
                                    placeholder="Material name"
                                    class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 text-sm">
                                <input type="number"
                                    wire:model="items.{{ $index }}.quantity"
                                    placeholder="Qty" min="1"
                                    class="w-20 px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 text-sm">
                                @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-red-500 hover:text-red-700 font-bold text-lg px-2">
                                        &times;
                                    </button>
                                @endif
                            </div>
                            @error("items.{$index}.name")
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            @error("items.{$index}.quantity")
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        @endforeach
                    </div>

                    <button type="button" wire:click="addItem"
                        class="mt-2 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        + Add Item
                    </button>
                </div>
                @endif

                {{-- Buttons --}}
                @if($request_type_id)
                <div class="flex gap-3 pt-2">
                    {{-- ✅ wire:loading disables button while submitting --}}
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        <span wire:loading.remove wire:target="submit">Submit Request</span>
                        <span wire:loading wire:target="submit">Submitting...</span>
                    </button>
                    <button type="button" wire:click="cancel"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                        Cancel
                    </button>
                </div>
                @endif

            </form>
        </div>
    </div>
</div>
</div>
