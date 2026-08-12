
<div class="select-none">
     <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                                    Users
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Manage and view all users
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden"
                                        href="{{ route('admin.user.create') }}">
                                         <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Add User
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Table -->
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th class="ps-6 py-3 text-left text-sm font-semibold text-gray-800 dark:text-neutral-200">Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800 dark:text-neutral-200">Email</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800 dark:text-neutral-200">Roles</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800 dark:text-neutral-200">Created</th>
                                    <th class="px-6 py-3 text-end text-sm font-semibold text-gray-800 dark:text-neutral-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($this->users as $user)
                                    <tr>
                                        <td class="ps-6 py-3">
                                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                {{ $user->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="text-sm text-gray-500 dark:text-neutral-400">
                                                {{ $user->email }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="text-sm text-gray-500 dark:text-neutral-400">
                                                {{ $user->roles->pluck('name')->join(', ') ?: 'No role assigned' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="text-sm text-gray-500 dark:text-neutral-400">
                                                {{ $user->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-end">
                                            <a href="{{ route('admin.user.edit', $user->id) }}"
                                               class="text-blue-600 hover:underline text-sm font-medium">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->

                        <!-- Footer -->
                        <div class="px-6 py-4 flex justify-between items-center border-t border-gray-200 dark:border-neutral-700">
                            <p class="text-sm text-gray-600 dark:text-neutral-400">
                                <span class="font-semibold text-gray-800 dark:text-neutral-200">{{ $this->users->count() }}</span> results
                            </p>
                            {{ $this->users->links() }}
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    {{-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca --}}
</div>
