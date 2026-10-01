<div class="select-none">
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
  <div class="flex flex-col">
    <div class="overflow-x-auto">
      <div class="min-w-full inline-block align-middle">
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">

          {{-- Header --}}
          <div class="px-6 py-4 flex items-center justify-between gap-4 border-b border-gray-200 dark:border-neutral-700">
            <div>
              <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                Materials Requests
              </h2>
              <p class="text-sm text-gray-600 dark:text-neutral-400">
                Review and manage material requests
              </p>
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
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Requestor</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Purpose</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Items</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Date Requested</th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
              @forelse($this->requests as $request)
                <tr wire:key="mat-{{ $request->id }}" class="hover:bg-gray-50 dark:hover:bg-neutral-700/40 transition align-top">

                  {{-- Requestor --}}
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                      <div class="shrink-0 size-8 rounded-full bg-green-700 text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($request->user->name, 0, 1)) }}
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 truncate">
                          {{ $request->user->name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">{{ $request->user->email }}</p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 truncate">
                          College of {{ $request->user->department->department_name ?? 'N/A' }}
                        </p>
                      </div>
                    </div>
                  </td>

                  {{-- Purpose --}}
                  <td class="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400 max-w-xs">
                    {{ \Illuminate\Support\Str::limit($request->purpose, 60) }}
                  </td>

                  {{-- Items --}}
                  <td class="px-6 py-3">
                    <ul class="text-xs text-gray-600 dark:text-neutral-400 space-y-1">
                      @foreach($request->items as $item)
                        <li class="flex items-center gap-1.5">
                          <span class="size-1 rounded-full bg-gray-300 dark:bg-neutral-600"></span>
                          <span class="truncate">{{ $item->item_name }}</span>
                          <span class="text-gray-400 dark:text-neutral-500 shrink-0">× {{ $item->quantity }}</span>
                        </li>
                      @endforeach
                    </ul>
                  </td>

                  {{-- Status --}}
                  <td class="px-6 py-3 whitespace-nowrap">
                    @if($request->status === 'pending')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                        Pending
                      </span>
                    @elseif($request->status === 'approved')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approved
                      </span>
                    @elseif($request->status === 'rejected')
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rejected
                      </span>
                    @elseif($request->status === 'cancelled')
                      <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        Cancelled
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ ucfirst($request->status) }}
                      </span>
                    @endif
                  </td>

                  {{-- Date --}}
                  <td class="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400 whitespace-nowrap">
                    @if($request->items->count() && $request->items->first()->request_date)
                      {{ \Carbon\Carbon::parse($request->items->first()->request_date)->format('M d, Y') }}
                    @else
                      <span class="text-gray-400">—</span>
                    @endif
                  </td>

                  {{-- Actions --}}
                  <td class="px-6 py-3 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">

                      {{-- ✅ View — new permission name --}}
                      @can('requests.view-all')
                        <a href="{{ route('admin.view-request', $request->id) }}"
                           class="px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                          View
                        </a>
                      @endcan

                      {{-- Accept / Reject (only if pending) --}}
                      @if($request->status === 'pending')
                        {{-- ✅ Accept — new permission name --}}
                        @can('requests.approve')
                          <button wire:click="accept({{ $request->id }})"
                                  wire:confirm="Accept this request?"
                                  class="px-2.5 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Accept
                          </button>
                        @endcan

                        {{-- ✅ Reject — new permission name --}}
                        @can('requests.reject')
                          <button wire:click="openReject({{ $request->id }})"
                                  class="px-2.5 py-1 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Reject
                          </button>
                        @endcan
                      @endif
                    </div>
                  </td>

                </tr>

                {{-- ✅ Inline Reject Form --}}
                @can('requests.reject')
                  @if ($rejectingRequestId === $request->id)
                    <tr class="bg-red-50/40 dark:bg-red-900/10">
                      <td colspan="6" class="px-6 py-4">
                        <div class="max-w-2xl bg-white dark:bg-neutral-800 border border-red-200 dark:border-red-900/50 rounded-lg p-4 space-y-3">
                          <div class="flex items-center gap-2">
                            <svg class="size-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <circle cx="12" cy="12" r="10" />
                              <path stroke-linecap="round" d="M12 8v4m0 4h.01" />
                            </svg>
                            <label class="text-sm font-semibold text-red-700 dark:text-red-400">
                              Reason for rejecting this material request
                            </label>
                            <span class="text-xs text-gray-400 dark:text-neutral-500">(optional)</span>
                          </div>

                          <textarea wire:model="rejectReason" rows="3"
                            placeholder="e.g. Insufficient stock, incorrect items, budget exceeded..."
                            class="w-full px-3 py-2 text-sm rounded-lg border border-red-200 dark:border-red-900/50 dark:bg-neutral-900 dark:text-neutral-200 focus:ring-2 focus:ring-red-300 focus:border-red-400"></textarea>

                          <p class="text-xs text-gray-500 dark:text-neutral-400">
                            This reason will be sent to <strong>{{ $request->user->name }}</strong> via in-app notification and email.
                          </p>

                          <div class="flex justify-end gap-2">
                            <button wire:click="cancelReject"
                              class="px-4 py-2 text-sm font-medium bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-neutral-300 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition">
                              Cancel
                            </button>
                            <button wire:click="confirmReject"
                              wire:loading.attr="disabled"
                              wire:loading.class="opacity-50 cursor-not-allowed"
                              class="px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                              <span wire:loading.remove wire:target="confirmReject">Confirm Rejection</span>
                              <span wire:loading wire:target="confirmReject">Rejecting...</span>
                            </button>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @endif
                @endcan

              @empty
                <tr>
                  <td colspan="6" class="px-6 py-14 text-center">
                    <div class="flex flex-col items-center gap-2">
                      <div class="size-12 rounded-full bg-gray-100 dark:bg-neutral-700 flex items-center justify-center">
                        <svg class="size-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                      </div>
                      <p class="text-sm font-medium text-gray-600 dark:text-neutral-400">No material requests</p>
                      <p class="text-xs text-gray-400 dark:text-neutral-500">Requests will appear here once Program Heads submit them.</p>
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
