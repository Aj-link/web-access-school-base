<div class="select-none">
<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Coordinator Requests</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Review and approve requests submitted by coordinators</p>
    </div>

    {{-- Stats --}}
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full px-4 py-2">
            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Pending: {{ $this->pendingCount }}</span>
        </div>
        <div class="bg-green-100 dark:bg-green-900/30 rounded-full px-4 py-2">
            <span class="text-sm font-medium text-green-800 dark:text-green-300">Approved: {{ $this->approvedCount }}</span>
        </div>
        <div class="bg-red-100 dark:bg-red-900/30 rounded-full px-4 py-2">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">Rejected: {{ $this->rejectedCount }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Search</label>
                <input type="text" wire:model.live="search" placeholder="Search by requester, purpose..."
                       class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
            </div>
            <div class="w-36">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-sm rounded-lg border">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="">All</option>
                </select>
            </div>
            <div>
                <button wire:click="clearFilters" class="px-4 py-2 text-sm bg-gray-500 text-white rounded-lg">Clear</button>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    {{-- Requests Table --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Requester</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Items / Facility</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Schedule</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->requests as $req)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $req->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $req->user->email }}</p>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $req->department->department_name ?? '—' }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full
                            {{ $req->requestType?->type_name === 'Facility Reservation' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $req->requestType?->type_name ?? '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">
                        @if($req->request_type_id == 1)
                            <p><strong>Facility:</strong> {{ $req->items->first()->item_name ?? 'N/A' }}</p>
                        @else
                            @foreach($req->items as $item)
                                <p>• {{ $item->item_name }} (x{{ $item->quantity }})</p>
                            @endforeach
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">
                        @php $first = $req->items->first(); @endphp
                        {{ $first ? \Carbon\Carbon::parse($first->request_date)->format('M d, Y') : '—' }}
                        @if($first && $first->start_time)
                            <br><span class="text-xs">{{ $first->start_time }} – {{ $first->end_time }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($req->status === 'pending')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                        @if($req->status === 'pending')
                            <button wire:click="approve({{ $req->id }})"
                                    wire:confirm="Approve this request?"
                                    class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                            <button wire:click="reject({{ $req->id }})"
                                    wire:confirm="Reject this request?"
                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                        @else
                            <span class="text-xs text-gray-400">No actions</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">No requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $this->requests->links() }}
        </div>
    </div>
</div>
</div>
