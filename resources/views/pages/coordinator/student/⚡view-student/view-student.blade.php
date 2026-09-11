<div>
<div class="max-w-5xl px-4 py-8 sm:px-6 mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Students & Faculty</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $this->departmentName }}</p>
        </div>
        <button wire:click="openCreate"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium bg-[#123524] text-white rounded-lg hover:bg-[#0C2418] transition">
            + Add Member
        </button>
    </div>

    {{-- Flash --}}
    @if(session()->has('success'))
        <div class="p-3 bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats: simple text row instead of 4 cards --}}
    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-neutral-400 border-b border-gray-200 dark:border-neutral-700 pb-4">
        <span><strong class="text-gray-800 dark:text-neutral-200">{{ $this->totalCount }}</strong> total</span>
        <span><strong class="text-gray-800 dark:text-neutral-200">{{ $this->studentCount }}</strong> students</span>
        <span><strong class="text-gray-800 dark:text-neutral-200">{{ $this->facultyCount }}</strong> faculty</span>
        @if($this->pendingCount > 0)
            <span class="text-yellow-700 dark:text-yellow-400">
                <strong>{{ $this->pendingCount }}</strong> pending approval
            </span>
        @endif
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-2">
        <input type="text" wire:model.live="search" placeholder="Search name or email..."
            class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
        <select wire:model.live="roleFilter"
            class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
            <option value="">All roles</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
        </select>
        <select wire:model.live="statusFilter"
            class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
            <option value="">All status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-gray-50 dark:bg-neutral-900/30 text-xs uppercase text-gray-500 dark:text-neutral-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left">Registered</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                    @forelse($this->students as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/30">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 dark:text-neutral-200">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $user->email }}</p>
                            </td>
                            <td class="hidden sm:table-cell px-4 py-3 text-gray-600 dark:text-neutral-400">
                                {{ ucfirst($user->roles->first()?->name ?? 'N/A') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium
                                    @if($user->status === 'approved') text-green-700 dark:text-green-400
                                    @elseif($user->status === 'pending') text-yellow-700 dark:text-yellow-400
                                    @else text-red-700 dark:text-red-400 @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 text-gray-400 dark:text-neutral-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <button wire:click="openEdit({{ $user->id }})" class="text-gray-500 hover:text-[#123524] dark:hover:text-green-400 text-xs font-medium">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $user->id }})"
                                    wire:confirm="Delete {{ $user->name }}? This cannot be undone."
                                    class="text-gray-500 hover:text-red-600 text-xs font-medium">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400 dark:text-neutral-500">
                                No members found.
                                <button wire:click="openCreate" class="block mx-auto mt-2 text-[#123524] dark:text-green-400 font-medium">
                                    + Add First Member
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->students->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
                {{ $this->students->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ── Create Modal ──────────────────────────────────── --}}
@if($showCreateModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6">
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl w-full max-w-sm max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-neutral-200">Add Member</h3>
            <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300">✕</button>
        </div>

        <div class="px-5 py-4 space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Full Name</label>
                <input type="text" wire:model="createName" placeholder="Juan Dela Cruz"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                @error('createName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Email</label>
                <input type="email" wire:model="createEmail" placeholder="user@csav.edu.ph"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                @error('createEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Password</label>
                <input type="password" wire:model="createPassword" placeholder="Min. 6 characters"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                @error('createPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Role</label>
                <select wire:model="createRole"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
                @error('createRole') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs text-gray-400 dark:text-neutral-500">
                Assigned to {{ $this->departmentName }} with status <strong>Pending</strong> until an admin approves.
            </p>
        </div>

        <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-neutral-700">
            <button wire:click="closeModals"
                class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600 text-gray-700 dark:text-neutral-300 rounded-lg">
                Cancel
            </button>
            <button wire:click="create" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm bg-[#123524] hover:bg-[#0C2418] text-white rounded-lg font-medium">
                <span wire:loading.remove wire:target="create">Create</span>
                <span wire:loading wire:target="create">Creating...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Edit Modal ────────────────────────────────────── --}}
@if($showEditModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6">
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl w-full max-w-sm max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-neutral-200">Edit Member</h3>
            <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300">✕</button>
        </div>

        <div class="px-5 py-4 space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Full Name</label>
                <input type="text" wire:model="editName"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                @error('editName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Email</label>
                <input type="email" wire:model="editEmail"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                @error('editEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Role</label>
                <select wire:model="editRole"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Status</label>
                <select wire:model="editStatus"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#1C6B45]">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                @error('editStatus') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-neutral-700">
            <button wire:click="closeModals"
                class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600 text-gray-700 dark:text-neutral-300 rounded-lg">
                Cancel
            </button>
            <button wire:click="update" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm bg-[#123524] hover:bg-[#0C2418] text-white rounded-lg font-medium">
                <span wire:loading.remove wire:target="update">Save</span>
                <span wire:loading wire:target="update">Saving...</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
