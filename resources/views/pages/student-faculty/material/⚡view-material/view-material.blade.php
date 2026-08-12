<div class="select-none">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header and Create Button --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Material Requests</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">View and manage your requested materials</p>
            </div>
            <a href="{{ route('portal.create-material') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Material Request
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-200">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Request Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items (Qty)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->materialRequests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $request->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $request->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    @foreach($request->items as $item)
                                        <div>{{ $item->item_name }} (x{{ $item->quantity }})</div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ $request->purpose }}
                                </td>
                                <td class="px-6 py-4">
                                    @php $status = $request->status; @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <a href="{{ route('portal.edit-material', $request->id) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Edit
                                        </a>
                                        <button wire:click="cancelRequest({{ $request->id }})"
                                                wire:confirm="Cancel this request?"
                                                class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                            Cancel
                                        </button>
                                    @elseif($request->status === 'cancelled')
                                        <button wire:click="deleteRequest({{ $request->id }})"
                                                wire:confirm="Permanently delete this cancelled request?"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-sm">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    You haven't made any material requests yet.
                                    <a href="{{ route('portal.create-material') }}" class="text-blue-600 hover:underline ml-1">Create one now</a>
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->materialRequests->links() }}
            </div>
        </div>
    </div>
</div>
