<div class="select-none">
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 sm:py-10 lg:px-8 lg:py-14">

    {{-- Page Header --}}
    <div class="mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Department Resources</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Materials allocated to your department from approved requests
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('message'))
        <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 sm:mb-8">

        {{-- Total Units --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 sm:gap-4">
            <div class="shrink-0 size-10 sm:size-12 rounded-lg bg-[#123524]/10 flex items-center justify-center">
                <svg class="size-5 sm:size-6 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Total Units Allocated
                </p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
                    {{ number_format($this->totalAllocated) }}
                </p>
            </div>
        </div>

        {{-- Distinct Resource Types --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 sm:gap-4">
            <div class="shrink-0 size-10 sm:size-12 rounded-lg bg-[#D4A537]/10 flex items-center justify-center">
                <svg class="size-5 sm:size-6 text-[#B8862A]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Resource Types
                </p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
                    {{ $this->allocations->total() }}
                </p>
            </div>
        </div>

    </div>

    {{-- Quick Materials Summary --}}
    @if($this->materialsSummary->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6 sm:mb-8">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="shrink-0 size-8 rounded-lg bg-[#123524]/10 flex items-center justify-center">
                <svg class="size-4 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">My Department's Materials</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Quick view of what your department currently holds</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($this->materialsSummary as $material)
                <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 px-4 py-3 hover:border-[#123524]/30 hover:bg-[#123524]/5 dark:hover:bg-[#123524]/10 transition">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                        {{ $material->resource_name }}
                    </span>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-1 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-full text-xs font-semibold">
                        {{ $material->formatted_quantity }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pending Facility Reservations --}}
@if($this->pendingFacilityRequests->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6 sm:mb-8">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pending Facility Reservations</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
            Approving locks in the room for that date and time — conflicting bookings are blocked automatically.
        </p>
    </div>

    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach($this->pendingFacilityRequests as $item)
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $item->facility_name }}
                        </span>
                        @if($item->has_conflict)
                            <span class="inline-flex items-center px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-full text-xs font-semibold">
                                Time Conflict — Already Booked
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-semibold">
                                Slot Free
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($item->request_date)->format('M d, Y') }}
                        &middot;
                        {{ \Carbon\Carbon::parse($item->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($item->end_time)->format('g:i A') }}
                        &middot;
                        Requested by {{ $item->requester_name }}
                    </p>
                    @if($item->purpose)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">"{{ Str::limit($item->purpose, 80) }}"</p>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        wire:click="approveRequest({{ $item->request_id }})"
                        wire:confirm="Approve this facility reservation for {{ $item->facility_name }}?"
                        @disabled($item->has_conflict)
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-[#123524] text-white hover:bg-[#123524]/90 disabled:opacity-40 disabled:cursor-not-allowed transition"
                    >
                        Approve
                    </button>
                    <button
                        type="button"
                        wire:click="openReject({{ $item->request_id }})"
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    >
                        Reject
                    </button>
                </div>
            </div>

            @if($rejectingRequestId === $item->request_id)
            <div class="mt-3 bg-gray-50 dark:bg-gray-900/40 rounded-md p-3 space-y-2">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">
                    Reason for rejecting (optional)
                </label>
                <textarea
                    wire:model="rejectRemarks"
                    rows="2"
                    class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:ring focus:ring-red-200 focus:border-red-300"
                    placeholder="e.g. Conflicts with another event, missing details, etc."
                ></textarea>
                <div class="flex gap-2">
                    <button type="button" wire:click="confirmReject"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition">
                        Confirm Reject
                    </button>
                    <button type="button" wire:click="cancelReject"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Department Resource Allocation</h3>
        </div>

        {{-- Desktop / tablet table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            Resource
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            Allocated Quantity
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->allocations as $allocation)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 size-8 rounded-md bg-[#123524]/5 flex items-center justify-center">
                                    <svg class="size-4 text-[#123524]/70" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                    {{ $allocation->resource_name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-semibold">
                                {{ $allocation->formatted_quantity }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="size-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="size-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    No resources have been allocated to your department yet.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile stacked cards --}}
        <div class="sm:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($this->allocations as $allocation)
            <div class="px-4 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 size-8 rounded-md bg-[#123524]/5 flex items-center justify-center">
                        <svg class="size-4 text-[#123524]/70" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                        {{ $allocation->resource_name }}
                    </span>
                </div>
                <span class="shrink-0 inline-flex items-center px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-semibold">
                    {{ $allocation->formatted_quantity }}
                </span>
            </div>
            @empty
            <div class="px-4 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="size-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="size-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No resources have been allocated to your department yet.
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        @if($this->allocations->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->allocations->links() }}
            </div>
        @endif
    </div>

</div>
</div>
