<div class="select-none">
    <div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Edit Material Request</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">Update your request details</p>
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
                    {{-- Purpose --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="purpose" rows="3"
                            placeholder="State your purpose for requesting these materials..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200"></textarea>
                        @error('purpose')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dynamic Items --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                Materials <span class="text-red-500">*</span>
                            </label>
                            <button type="button" wire:click="addItem"
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                + Add Item
                            </button>
                        </div>

                        @foreach($items as $index => $item)
                            <div class="flex gap-3 items-start">
                                <div class="flex-1">
                                    <input type="text" wire:model="items.{{ $index }}.name"
                                        placeholder="Material name"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                                    @error("items.{$index}.name")
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="w-28">
                                    <input type="number" wire:model="items.{{ $index }}.quantity"
                                        placeholder="Qty" min="1"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200">
                                    @error("items.{$index}.quantity")
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="mt-2 text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Update Request
                        </button>
                        <button type="button" wire:click="cancel"
                            class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
