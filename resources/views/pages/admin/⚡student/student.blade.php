<div class="select-none">
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">User Management</h2>
                <p class="text-sm text-gray-500 dark:text-neutral-400">Approve or reject student, faculty, and coordinator accounts</p>
            </div>

            {{-- Search --}}
            <div class="flex flex-wrap items-center gap-2">
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name or email..."
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 w-52 focus:outline-none focus:ring-2 focus:ring-green-500">

                {{-- Role Filter --}}
                <select wire:model.live="roleFilter"
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">All Roles</option>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="program head">Program Head</option>
                </select>

                {{-- Status Filter --}}
                <select wire:model.live="statusFilter"
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-800 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Registered</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse($this->users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">

                        {{-- Name --}}
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-700 text-white flex items-center justify-center text-sm font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $user->name }}</p>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400">
                            {{ $user->email }}
                        </td>

                        {{-- Role --}}
                        <td class="px-6 py-3">
                            @php $role = $user->roles->first()?->name ?? 'N/A'; @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($role === 'student') bg-blue-100 text-blue-700
                                @elseif($role === 'faculty') bg-purple-100 text-purple-700
                                @elseif($role === 'program head') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst($role) }}
                            </span>
                        </td>

                        {{-- Department --}}
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400">
                            {{ $user->department->department_name ?? 'N/A' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($user->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($user->status === 'approved') bg-green-100 text-green-700
                                @elseif($user->status === 'rejected') bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>

                        {{-- Registered --}}
                        <td class="px-6 py-3 text-sm text-gray-400">
                            {{ $user->created_at->diffForHumans() }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-3 text-right space-x-2">
                            @if($user->status === 'pending')
                                <button wire:click="approve({{ $user->id }})"
                                    wire:confirm="Approve {{ $user->name }}?"
                                    class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs transition">
                                    Approve
                                </button>
                                <button wire:click="reject({{ $user->id }})"
                                    wire:confirm="Reject {{ $user->name }}?"
                                    class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs transition">
                                    Reject
                                </button>
                            @elseif($user->status === 'approved')
                                <span class="text-xs text-green-600 font-medium">✓ Approved</span>
                            @elseif($user->status === 'rejected')
                                <span class="text-xs text-red-500 font-medium">✗ Rejected</span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2h5"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">No users found</p>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
            {{ $this->users->links() }}
        </div>

    </div>
</div>
</div>
