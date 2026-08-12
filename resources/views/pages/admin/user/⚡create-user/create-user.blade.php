
<div class="select-none">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="mt-12 max-w-full mx-auto">
        <div class="flex flex-col border border-gray-200 rounded-xl p-4 sm:p-6 lg:p-8 dark:border-neutral-700">
            <h2 class="mb-8 text-xl font-semibold text-gray-800 dark:text-neutral-200">
                Create User
            </h2>

            @if (session('success'))
                <div class="mb-4 text-green-600 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="grid gap-4 lg:gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Name</label>
                        <input wire:model.defer="name" type="text"
                            class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Email</label>
                        <input wire:model.defer="email" type="email"
                            class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Password</label>
                        <div class="relative">
                            <input wire:model.defer="password" type="password" id="password"
                                class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 px-3 text-sm text-gray-600 dark:text-neutral-300">
                                Show
                            </button>
                        </div>
                        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Confirm Password</label>
                        <input wire:model.defer="password_confirmation" type="password"
                            class="py-2.5 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                    </div>

                    <!-- Assign Role -->
                    <div class="mt-5">
                        <h2 class="mb-1 text-lg font-semibold text-gray-800 dark:text-neutral-200">
                            Assign Role
                        </h2>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <label class="flex items-center p-3 w-full border rounded-lg text-sm dark:border-neutral-700">
                                    <input type="radio" wire:model="selectedRole" value="{{ $role->name }}"
                                        class="shrink-0 size-4 border-gray-300 rounded-sm text-blue-600 focus:ring-blue-500">
                                    <span class="ms-3 text-sm text-gray-700 dark:text-neutral-200">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedRole') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-6 grid">
                    <button type="submit"
                        class="w-50 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    {{-- Always remember that you are absolutely unique. Just like everyone else. - Margaret Mead --}}
</div>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const button = input.nextElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = 'Hide';
    } else {
        input.type = 'password';
        button.textContent = 'Show';
    }
}
</script>
