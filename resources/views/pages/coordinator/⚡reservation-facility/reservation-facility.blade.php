<div class="select-none">
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
  <div class="flex flex-col">
    <div class="overflow-x-auto">
      <div class="min-w-full inline-block align-middle">
        <div class="bg-white dark:bg-neutral-800 border rounded-xl shadow overflow-hidden">

          {{-- Header with Facility/Material tabs --}}
          <div class="px-6 py-4 flex justify-between items-center border-b">
            <div>
              <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Reservation Facilities</h2>
              <p class="text-sm text-gray-600 dark:text-neutral-400">Review and manage facility reservations from students</p>
            </div>

            <div class="flex items-center gap-1 bg-gray-100 dark:bg-neutral-700 rounded-lg p-1">
              <a href="{{ route('coordinator.facility') }}" wire:navigate
                 class="px-4 py-1.5 text-sm font-semibold rounded-md transition
                   {{ request()->routeIs('coordinator.facility')
                        ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow'
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200' }}">
                Facility
              </a>
              <a href="{{ route('coordinator.material') }}" wire:navigate
                 class="px-4 py-1.5 text-sm font-semibold rounded-md transition
                   {{ request()->routeIs('coordinator.material')
                        ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow'
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200' }}">
                Material
              </a>
            </div>
          </div>

          {{-- Flash messages --}}
          @if (session('message'))
            <div class="px-6 py-3 bg-green-50 dark:bg-green-900/20 border-b border-green-100 dark:border-green-900 text-sm text-green-700 dark:text-green-400">
              {{ session('message') }}
            </div>
          @endif
          @if (session('error'))
            <div class="px-6 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900 text-sm text-red-700 dark:text-red-400">
              {{ session('error') }}
            </div>
          @endif

          {{-- Table --}}
          <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Requestor</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Facility</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Schedule</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Purpose</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
              @forelse($this->reservations as $reservation)
                @php
                    $facilityItem = $reservation->items->firstWhere('resource_id', null);
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition align-top">

                  {{-- Requestor --}}
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                      <div class="shrink-0 size-8 rounded-full bg-[#123524] text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($reservation->user->name, 0, 1)) }}
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 truncate">{{ $reservation->user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $reservation->user->email }}</p>
                      </div>
                    </div>
                  </td>

                  {{-- Facility --}}
                  <td class="px-6 py-3 text-sm text-gray-700 dark:text-neutral-300">
                    {{ $facilityItem->item_name ?? '—' }}
                  </td>

                  {{-- Schedule --}}
                  <td class="px-6 py-3 text-xs text-gray-600 dark:text-neutral-400 whitespace-nowrap">
                    @if($facilityItem && $facilityItem->request_date)
                      <p class="font-medium text-gray-700 dark:text-neutral-300">
                        {{ \Carbon\Carbon::parse($facilityItem->request_date)->format('M d, Y') }}
                      </p>
                      @if($facilityItem->start_time && $facilityItem->end_time)
                        <p class="text-gray-500 dark:text-neutral-500">
                          {{ \Carbon\Carbon::parse($facilityItem->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($facilityItem->end_time)->format('g:i A') }}
                        </p>
                      @endif
                    @else
                      <span class="text-gray-400">—</span>
                    @endif
                  </td>

                  {{-- Purpose --}}
                  <td class="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400 max-w-xs">
                    {{ \Illuminate\Support\Str::limit($reservation->purpose, 60) }}
                  </td>

                  {{-- Status ── NEW --}}
                  <td class="px-6 py-3 whitespace-nowrap">
                    @if($reservation->status === 'pending')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                        Pending
                      </span>
                    @elseif($reservation->status === 'approved')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approved
                      </span>
                    @elseif($reservation->status === 'rejected')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rejected
                      </span>
                    @elseif($reservation->status === 'cancelled')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        Cancelled
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ ucfirst($reservation->status) }}
                      </span>
                    @endif
                  </td>

                  {{-- Actions --}}
                  <td class="px-6 py-3 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                      <a href="{{ route('coordinator.view-request-reserve', $reservation->id) }}" wire:navigate
                         class="px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                        View
                      </a>
                      @if($reservation->status === 'pending')
                        <button wire:click="accept({{ $reservation->id }})"
                                wire:confirm="Accept this reservation?"
                                class="px-2.5 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                          Accept
                        </button>
                        <button wire:click="reject({{ $reservation->id }})"
                                wire:confirm="Reject this reservation?"
                                class="px-2.5 py-1 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                          Reject
                        </button>
                      @endif
                    </div>
                  </td>

                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-6 py-10 text-center">
                    <div class="flex flex-col items-center gap-2">
                      <div class="size-12 rounded-full bg-gray-100 dark:bg-neutral-700 flex items-center justify-center">
                        <svg class="size-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                      <p class="text-sm font-medium text-gray-600 dark:text-neutral-400">No facility reservations yet</p>
                      <p class="text-xs text-gray-400 dark:text-neutral-500">Reservations from students and faculty will appear here</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</div>
</div>