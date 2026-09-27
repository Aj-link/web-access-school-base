<div class="select-none">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="mt-12 max-w-full mx-auto">
            <div class="flex flex-col border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 dark:border-neutral-700">

                {{-- Header with Back button --}}
                <div class="mb-8 flex items-center justify-between gap-x-2">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                            Edit User
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                            Update the account details or change the user's role
                        </p>
                    </div>

                    <a href="{{ route('admin.users') }}"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"/>
                            <path d="M19 12H5"/>
                        </svg>
                        Back
                    </a>
                </div>

                @if (session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- User info strip --}}
                <div class="mb-6 p-4 rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-700 text-white flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ $user->email }}</p>
                    </div>
                    @if ($this->isAdmin)
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 shrink-0">
                            Admin — read only
                        </span>
                    @endif
                </div>

                <form wire:submit.prevent="save">
                    <div class="grid gap-4 lg:gap-6">

                        {{-- Role (hidden for admin) --}}
                        @unless ($this->isAdmin)
                            <div>
                                <label class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    @foreach ($this->roles as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model.live="role" value="{{ $value }}" class="peer sr-only">
                                            <div class="text-center py-3 px-4 text-sm font-medium rounded-lg border-2 border-gray-200 dark:border-neutral-700 text-gray-600 dark:text-neutral-300 bg-white dark:bg-neutral-900 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white hover:border-blue-300 dark:hover:border-blue-700 transition">
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('role') <span class="text-red-600 text-sm block mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endunless

                        {{-- Name + Email --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="hs-name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="hs-name" wire:model="name"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="hs-email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="hs-email" wire:model="email"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="hs-password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
                                    New Password
                                </label>
                                <div class="relative" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" id="hs-password" wire:model="password"
                                        placeholder="Leave blank to keep current"
                                        class="py-2.5 px-4 pr-12 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-xs text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200">
                                        <span x-text="show ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="hs-password-confirmation" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
                                    Confirm New Password
                                </label>
                                <input type="password" id="hs-password-confirmation" wire:model="password_confirmation"
                                    placeholder="Re-enter new password"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                            </div>
                        </div>

                        {{-- Department (hidden for admin) --}}
                        @unless($this->isAdmin)
                            <div>
                                <label for="hs-department" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
                                    Department <span class="text-red-500">*</span>
                                </label>
                                <select id="hs-department" wire:model="department_id"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                    <option value="">Select department</option>
                                    @foreach($this->departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endunless

                        {{-- Info note --}}
                        <div class="flex items-start gap-2.5 rounded-lg border border-blue-200 dark:border-blue-900/50 bg-blue-50 dark:bg-blue-900/20 px-3.5 py-3">
                            <svg class="size-4 shrink-0 mt-0.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path stroke-linecap="round" d="M12 16v-4m0-4h.01" />
                            </svg>
                            <p class="text-xs text-blue-800 dark:text-blue-300">
                                Leave the password fields blank to keep the user's current password.
                            </p>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="mt-6 flex gap-3">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden transition">
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                        <a href="{{ route('admin.users') }}"
                            class="py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
