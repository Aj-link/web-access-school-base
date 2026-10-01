<div class="select-none">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Back --}}
    <a href="{{ $isFacility ? route('portal.reservation') : route('portal.material') }}" wire:navigate
       class="group inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 dark:text-neutral-400 dark:hover:text-white transition mb-6">
        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 group-hover:-translate-x-0.5 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </span>
        Back to my {{ $isFacility ? 'facility reservations' : 'material requests' }}
    </a>

    {{-- Flash messages --}}
    @if (session()->has('message'))
        <div class="mb-4 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 ring-1 ring-inset ring-emerald-200 dark:ring-emerald-900 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 flex items-center gap-3 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 text-rose-800 dark:text-rose-300 ring-1 ring-inset ring-rose-200 dark:ring-rose-900 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @php
        $status = $requestModel->status;
        $facilityItem  = $requestModel->items->firstWhere('resource_id', null);
        $materialItems = $requestModel->items->whereNotNull('resource_id');

        $statusStyles = [
            'pending'   => ['bg' => 'bg-amber-50 dark:bg-amber-950/30',     'text' => 'text-amber-700 dark:text-amber-400',     'ring' => 'ring-amber-200 dark:ring-amber-900',     'dot' => 'bg-amber-500',   'bar' => 'from-amber-400 to-orange-400',  'label' => 'Pending review'],
            'approved'  => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'ring' => 'ring-emerald-200 dark:ring-emerald-900', 'dot' => 'bg-emerald-500', 'bar' => 'from-emerald-400 to-teal-400', 'label' => 'Approved'],
            'rejected'  => ['bg' => 'bg-rose-50 dark:bg-rose-950/30',       'text' => 'text-rose-700 dark:text-rose-400',       'ring' => 'ring-rose-200 dark:ring-rose-900',       'dot' => 'bg-rose-500',    'bar' => 'from-rose-400 to-pink-400',     'label' => 'Rejected'],
            'cancelled' => ['bg' => 'bg-slate-100 dark:bg-neutral-800',     'text' => 'text-slate-600 dark:text-neutral-300',   'ring' => 'ring-slate-200 dark:ring-neutral-700',   'dot' => 'bg-slate-400',   'bar' => 'from-slate-300 to-slate-400',   'label' => 'Cancelled'],
        ];
        $s = $statusStyles[$status] ?? $statusStyles['pending'];
    @endphp

    <div class="bg-white dark:bg-neutral-900 border border-slate-200/80 dark:border-neutral-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">

        {{-- Status accent bar --}}
        <div class="h-1.5 bg-gradient-to-r {{ $s['bar'] }}"></div>

        {{-- Header --}}
        <div class="px-6 sm:px-8 pt-7 pb-6 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-start gap-4">
            <div class="min-w-0">
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                    {{ $isFacility ? 'Facility reservation' : 'Material request' }}
                </h2>
                <p class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-neutral-400 mt-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Submitted {{ $requestModel->created_at->format('M d, Y') }} at {{ $requestModel->created_at->format('h:i A') }}
                </p>
            </div>

            <span class="self-start shrink-0 inline-flex items-center gap-2 px-3.5 py-1.5 text-sm font-medium rounded-full ring-1 ring-inset {{ $s['bg'] }} {{ $s['text'] }} {{ $s['ring'] }}">
                <span class="relative flex w-2 h-2">
                    @if($status === 'pending')
                        <span class="absolute inline-flex w-full h-full rounded-full opacity-60 animate-ping {{ $s['dot'] }}"></span>
                    @endif
                    <span class="relative inline-flex w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                </span>
                {{ $s['label'] }}
            </span>
        </div>

        <div class="px-6 sm:px-8 pb-8 space-y-6">

            {{-- Rejection reason (only when rejected) --}}
            @if ($status === 'rejected' && $this->rejection?->remarks)
                <div class="flex items-start gap-4 p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/20 ring-1 ring-inset ring-rose-200 dark:ring-rose-900/50">
                    <div class="shrink-0 w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path stroke-linecap="round" d="M12 8v4m0 4h.01" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">Why it was rejected</p>
                        <p class="text-sm text-rose-900 dark:text-rose-200 mt-1 whitespace-pre-wrap break-words">{{ $this->rejection->remarks }}</p>
                        @if ($this->rejection->approver)
                            <p class="text-xs text-rose-600/80 dark:text-rose-400/80 mt-3">
                                Rejected by <span class="font-semibold">{{ $this->rejection->approver->name }}</span>
                                @if ($this->rejection->approved_at)
                                    · {{ \Carbon\Carbon::parse($this->rejection->approved_at)->diffForHumans() }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Purpose --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-2">Purpose</h3>
                <p class="text-sm leading-relaxed text-slate-600 dark:text-neutral-300 break-words">{{ $requestModel->purpose }}</p>
            </section>

            {{-- Facility schedule --}}
            @if($isFacility && $facilityItem)
                <section>
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11l6 3v7M9 9v.01M9 12v.01M9 15v.01" />
                        </svg>
                        Facility and schedule
                    </h3>
                    <div class="rounded-2xl border border-slate-200 dark:border-neutral-800 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-base font-semibold text-slate-900 dark:text-neutral-100">{{ $facilityItem->item_name }}</p>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                </svg>
                                {{ \Illuminate\Support\Carbon::parse($facilityItem->request_date)->format('M d, Y') }}
                            </span>
                            @if ($facilityItem->start_time)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ \Illuminate\Support\Carbon::parse($facilityItem->start_time)->format('h:i A') }}
                                    – {{ \Illuminate\Support\Carbon::parse($facilityItem->end_time)->format('h:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            {{-- Materials --}}
            @if($materialItems->isNotEmpty())
                <section>
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        {{ $isFacility ? 'Materials requested' : 'Items' }}
                        <span class="text-xs font-medium text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-neutral-800 px-2 py-0.5 rounded-full">{{ $materialItems->count() }}</span>
                    </h3>
                    <ul class="rounded-2xl border border-slate-200 dark:border-neutral-800 divide-y divide-slate-100 dark:divide-neutral-800 overflow-hidden">
                        @foreach($materialItems as $item)
                            <li class="px-4 sm:px-5 py-3 flex items-center justify-between gap-4">
                                <p class="text-sm text-slate-700 dark:text-neutral-300">{{ $item->item_name }}</p>
                                <span class="shrink-0 text-xs font-semibold text-slate-700 dark:text-neutral-200 bg-slate-100 dark:bg-neutral-800 px-2.5 py-1 rounded-lg">×{{ $item->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Plain material request with no facility item at all --}}
            @if(!$isFacility && $materialItems->isEmpty() && $requestModel->items->isNotEmpty())
                <section>
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Items
                        <span class="text-xs font-medium text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-neutral-800 px-2 py-0.5 rounded-full">{{ $requestModel->items->count() }}</span>
                    </h3>
                    <ul class="rounded-2xl border border-slate-200 dark:border-neutral-800 divide-y divide-slate-100 dark:divide-neutral-800 overflow-hidden">
                        @foreach($requestModel->items as $item)
                            <li class="px-4 sm:px-5 py-3 flex items-center justify-between gap-4">
                                <p class="text-sm text-slate-700 dark:text-neutral-300">{{ $item->item_name }}</p>
                                <span class="shrink-0 text-xs font-semibold text-slate-700 dark:text-neutral-200 bg-slate-100 dark:bg-neutral-800 px-2.5 py-1 rounded-lg">×{{ $item->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

        </div>

        {{-- Actions --}}
        @if($status === 'pending' || $status === 'cancelled')
            <div class="px-6 sm:px-8 py-5 bg-slate-50 dark:bg-neutral-800/40 border-t border-slate-100 dark:border-neutral-800 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-sm text-slate-500 dark:text-neutral-400">
                    {{ $status === 'pending' ? 'You can still edit or cancel this request.' : 'This request was cancelled. You can delete it permanently.' }}
                </p>

                <div class="flex gap-2">
                    @if($status === 'pending')
                        <a href="{{ $isFacility ? route('portal.edit-reservation', $requestModel->id) : route('portal.edit-material', $requestModel->id) }}" wire:navigate
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-medium bg-white dark:bg-neutral-900 text-indigo-600 dark:text-indigo-400 border border-slate-200 dark:border-neutral-700 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 dark:hover:bg-indigo-950/30 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2 2 0 112.828 2.828L11.828 15.83a2 2 0 01-.86.5L7 17l.67-3.97a2 2 0 01.5-.86z" />
                            </svg>
                            Edit
                        </a>
                        <button wire:click="cancelRequest"
                                wire:confirm="Cancel this request?"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-medium bg-amber-500 text-white rounded-xl shadow-md shadow-amber-500/20 hover:bg-amber-600 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                    @elseif($status === 'cancelled')
                        <button wire:click="deleteRequest"
                                wire:confirm="Permanently delete this cancelled request?"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-medium bg-rose-600 text-white rounded-xl shadow-md shadow-rose-600/20 hover:bg-rose-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
</div>
