<div class="select-none">
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 py-4 flex justify-between items-center border-b">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Departments</h2>
                            <p class="text-sm text-gray-600 dark:text-neutral-400">List of all school departments</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                {{ $this->departments->count() }} Total
                            </span>
                            <a href="{{ route('admin.departments.create') }}"
                                class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                + Add Department
                            </a>
                        </div>
                    </div>

                    {{-- Table --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Department Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Students</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($this->departments as $index => $dept)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            College of {{ $dept->department_name }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                            {{ $dept->users_count }} students
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right space-x-2">
                                        <a href="{{ route('admin.departments.edit', $dept->id) }}"
                                            class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Edit
                                        </a>
                                        <button wire:click="delete({{ $dept->id }})"
                                            wire:confirm="Delete this department? Students will lose their assignment."
                                            class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                        No departments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
