<div class="select-none">
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

                    <div class="px-6 py-4 flex justify-between items-center border-b">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">My Reservations</h2>
                            <p class="text-sm text-gray-600 dark:text-neutral-400">Track your facility requests</p>
                        </div>
                        <a href="{{ route('portal.create-reservation') }}"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            + Create Reservation
                        </a>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Facility</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Requested On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($this->reservations as $index => $request)
                                @foreach($request->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $loop->parent->index + 1 }}</td>
                                    <td class="px-6 py-3">
                                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            {{ $item->item_name }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($item->request_date)->format('M d, Y') }}<br>
                                        <span class="text-xs">{{ $item->start_time }} – {{ $item->end_time }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $request->purpose }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @php $status = $request->status; @endphp
                                        @if($status === 'pending')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                        @elseif($status === 'approved')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Approved</span>
                                        @elseif($status === 'rejected')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejected</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">{{ ucfirst($status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ $request->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        No reservations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="px-6 py-4 border-t">
                        {{ $this->reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
