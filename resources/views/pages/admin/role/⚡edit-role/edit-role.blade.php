
<div class="select-none">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="mt-12 max-w-full mx-auto">
            <div class="flex flex-col border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 dark:border-neutral-700">
                <h2 class="mb-8 text-xl font-semibold text-gray-800 dark:text-neutral-200">
                    Edit Role & Permission
                </h2>

                <form wire:submit.prevent="save">
                    <div class="grid gap-4 lg:gap-6">
                        <!-- Role Name -->
                        <div>
                            <label for="hs-name" class="block mb-2 text-sm font-medium dark:text-white">Role Name</label>
                            <input wire:model.defer="name" type="text" id="hs-name"
                                class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mt-5">
                            <h2 class="mb-1 text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                Assign Permissions
                            </h2>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
                                @foreach ($this->permissions as $key => $permission)
                                    <label wire:key="{{ $key }}" for="perm-{{ $key }}"
                                        class="flex items-center p-3 w-full border rounded-lg text-sm dark:border-neutral-700">
                                        <input type="checkbox" id="perm-{{ $key }}"
                                            wire:model="selectedPermissions" value="{{ $permission->name }}"
                                            class="shrink-0 size-4 border-gray-300 rounded-sm text-blue-600 focus:ring-blue-500">
                                        <span class="ms-3 text-sm text-gray-700 dark:text-neutral-200">
                                            {{ str_replace('_', ' ', Str::title($permission->name)) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedPermissions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6">
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Save</button>
                    </div>

                    @if(session()->has('success'))
                        <div class="mt-4 text-green-600">{{ session('success') }}</div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    {{-- Always remember that you are absolutely unique. Just like everyone else. - Margaret Mead --}}
</div>
