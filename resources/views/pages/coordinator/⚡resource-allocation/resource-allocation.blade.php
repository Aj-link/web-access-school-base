<div class="select-none">
<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Resource Allocations</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Allocate resources to departments</p>
        </div>
        <button wire:click="openCreateModal"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
            + New Allocation
        </button>
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Resource</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Allocated Quantity</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->allocations as $allocation)
                <tr>
                    <td class="px-6 py-3 text-sm text-gray-800">{{ $allocation->resource->resource_name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $allocation->department->department_name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $allocation->allocated_quantity }}</td>
                    <td class="px-6 py-3 text-right space-x-2">
                        <button wire:click="openEditModal({{ $allocation->id }})"
                            class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                        <button wire:click="delete({{ $allocation->id }})"
                            wire:confirm="Delete this allocation?"
                            class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">No allocations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $this->allocations->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" wire:click="closeModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ $editId ? 'Edit' : 'New' }} Resource Allocation</h3>
                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Resource</label>
                            <select wire:model="resource_id" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Resource</option>
                                @foreach($this->resources as $resource)
                                    <option value="{{ $resource->id }}">{{ $resource->resource_name }} (Available: {{ $resource->quantity_available }})</option>
                                @endforeach
                            </select>
                            @error('resource_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Department</label>
                            <select wire:model="department_id" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Department</option>
                                @foreach($this->departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Allocated Quantity</label>
                            <input type="number" wire:model="allocated_quantity" min="1" class="w-full px-3 py-2 border rounded-lg">
                            @error('allocated_quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">{{ $editId ? 'Update' : 'Create' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>
