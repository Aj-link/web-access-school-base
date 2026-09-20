<div class="select-none">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="mt-12 max-w-full mx-auto">
            <div class="flex flex-col border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 dark:border-neutral-700">

                <!-- Header with Back button -->
                <div class="mb-8 flex items-center justify-between gap-x-2">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                        Edit User
                    </h2>

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
                                    placeholder="Leave blank to keep current password"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="hs-password-confirmation" class="block mb-2 text-sm font-medium dark:text-white">Confirm Password</label>
                                <input type="password" id="hs-password-confirmation" wire:model="password_confirmation"
                                    class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                            </div>
                        </div>

                        <!-- Department (hidden for admin) -->
                        @unless($this->isAdmin)
                            <div>
                                <label for="hs-department" class="block mb-2 text-sm font-medium dark:text-white">Department</label>
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
