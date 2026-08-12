
<div class="select-none">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="mt-12 max-w-full mx-auto">
            <div class="flex flex-col border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 dark:border-neutral-700">
                <h2 class="mb-8 text-xl font-semibold text-gray-800 dark:text-neutral-200">
                    Edit User
                </h2>

                @if (session('success'))
                    <div class="mb-4 text-green-600 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <div class="grid gap-4 lg:gap-6">
                        <!-- Name & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="hs-name" class="block mb-2 text-sm font-medium dark:text-white">Name</label>
                                <input type="text" id="hs-name" wire:model="name"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="hs-email" class="block mb-2 text-sm font-medium dark:text-white">Email</label>
                                <input type="email" id="hs-email" wire:model="email"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="hs-password" class="block mb-2 text-sm font-medium dark:text-white">Password</label>
                                <input type="password" id="hs-password" wire:model="password"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="hs-password-confirmation" class="block mb-2 text-sm font-medium dark:text-white">Confirm Password</label>
                                <input type="password" id="hs-password-confirmation" wire:model="password_confirmation"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                            </div>
                        </div>

                        <!-- Roles -->
                        <div class="mt-5">
                            <h2 class="mb-1 text-lg font-semibold text-gray-800 dark:text-neutral-200">Assign Roles</h2>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                    <label for="role-{{ $role->id }}" class="flex items-center p-3 w-full border rounded-lg text-sm dark:border-neutral-700">
                                        <input type="checkbox" id="role-{{ $role->id }}" wire:model="selectedRoles" value="{{ $role->name }}"
                                            class="shrink-0 size-4 border-gray-300 rounded-sm text-blue-600 focus:ring-blue-500">
                                        <span class="ms-3 text-sm text-gray-700 dark:text-neutral-200">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedRoles') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 grid">
                        <button type="submit"
                            class="w-50 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh --}}
</div>
</div>
