<div class="select-none">
<div class="max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12 mx-auto">

  {{-- Back --}}
  <a href="{{ route('coordinator.facility') }}" wire:navigate
     class="group inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 dark:text-neutral-400 dark:hover:text-white transition mb-6">
    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 group-hover:-translate-x-0.5 transition">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </span>
    Back to reservations
  </a>

  @php
    $status = $requestModel->status;
    $statusStyles = [
      'pending'  => ['bg' => 'bg-amber-50 dark:bg-amber-950/30',  'text' => 'text-amber-700 dark:text-amber-400',  'ring' => 'ring-amber-200 dark:ring-amber-900',  'dot' => 'bg-amber-500',  'bar' => 'from-amber-400 to-orange-400',  'label' => 'Pending review'],
      'approved' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'ring' => 'ring-emerald-200 dark:ring-emerald-900', 'dot' => 'bg-emerald-500', 'bar' => 'from-emerald-400 to-teal-400', 'label' => 'Approved'],
      'rejected' => ['bg' => 'bg-rose-50 dark:bg-rose-950/30',    'text' => 'text-rose-700 dark:text-rose-400',    'ring' => 'ring-rose-200 dark:ring-rose-900',    'dot' => 'bg-rose-500',   'bar' => 'from-rose-400 to-pink-400',     'label' => 'Rejected'],
    ];
    $s = $statusStyles[$status] ?? $statusStyles['pending'];

    $facilityItem  = $requestModel->items->firstWhere('resource_id', null);
    $materialItems = $requestModel->items->whereNotNull('resource_id');
  @endphp

  <div class="bg-white dark:bg-neutral-900 border border-slate-200/80 dark:border-neutral-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">

    {{-- Status accent bar --}}
    <div class="h-1.5 bg-gradient-to-r {{ $s['bar'] }}"></div>

    {{-- Header --}}
    <div class="px-6 sm:px-8 pt-7 pb-6 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-start gap-4">
      <div class="min-w-0">
        <p class="text-sm text-slate-400 dark:text-neutral-500 mb-1.5">Reservation request</p>
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white break-words">{{ $requestModel->purpose }}</h2>
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

      {{-- Requestor / Department --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800/60">
          <div class="w-12 h-12 shrink-0 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-lg font-semibold text-white shadow-md shadow-indigo-500/20">
            {{ strtoupper(substr($requestModel->user->name, 0, 1)) }}
          </div>
          <div class="min-w-0">
            <p class="text-xs text-slate-400 dark:text-neutral-500">Requested by</p>
            <p class="text-sm font-semibold text-slate-900 dark:text-neutral-100 truncate">{{ $requestModel->user->name }}</p>
            <p class="text-xs text-slate-500 dark:text-neutral-400 truncate">{{ $requestModel->user->email }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800/60">
          <div class="w-12 h-12 shrink-0 rounded-2xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-neutral-400">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-xs text-slate-400 dark:text-neutral-500">Department</p>
            <p class="text-sm font-semibold text-slate-900 dark:text-neutral-100">{{ $requestModel->user->department->department_name ?? 'N/A' }}</p>
          </div>
        </div>
      </div>

      {{-- Facility / Materials --}}
      @if($facilityItem || $materialItems->isNotEmpty())

        @if($facilityItem)
        <section>
          <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-3">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11l6 3v7M9 9v.01M9 12v.01M9 15v.01" />
            </svg>
            Facility
          </h3>
          <div class="rounded-2xl border border-slate-200 dark:border-neutral-800 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-base font-semibold text-slate-900 dark:text-neutral-100">{{ $facilityItem->item_name }}</p>

            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                </svg>
                {{ \Carbon\Carbon::parse($facilityItem->request_date)->format('M d, Y') }}
              </span>
              @if($facilityItem->start_time && $facilityItem->end_time)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 text-sm font-medium">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ \Carbon\Carbon::parse($facilityItem->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($facilityItem->end_time)->format('h:i A') }}
                </span>
              @endif
            </div>
          </div>
        </section>
        @endif

        @if($materialItems->isNotEmpty())
        <section>
          <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-neutral-100 mb-3">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Materials
            <span class="text-xs font-medium text-slate-500 dark:text-neutral-400 bg-slate-100 dark:bg-neutral-800 px-2 py-0.5 rounded-full">{{ $materialItems->count() }}</span>
          </h3>
          <ul class="rounded-2xl border border-slate-200 dark:border-neutral-800 divide-y divide-slate-100 dark:divide-neutral-800 overflow-hidden">
            @foreach($materialItems as $material)
              <li class="px-4 sm:px-5 py-3 flex items-center justify-between gap-4">
                <p class="text-sm text-slate-700 dark:text-neutral-300">{{ $material->item_name }}</p>
                <span class="shrink-0 text-xs font-semibold text-slate-700 dark:text-neutral-200 bg-slate-100 dark:bg-neutral-800 px-2.5 py-1 rounded-lg">×{{ $material->quantity }}</span>
              </li>
            @endforeach
          </ul>
        </section>
        @endif

      @endif

    </div>

    {{-- Actions --}}
    @if($requestModel->status === 'pending')
    <div class="px-6 sm:px-8 py-5 bg-slate-50 dark:bg-neutral-800/40 border-t border-slate-100 dark:border-neutral-800 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
      <p class="text-sm text-slate-500 dark:text-neutral-400">This request is waiting for your decision.</p>

      <div class="flex gap-2">
        <button wire:click="reject"
                wire:confirm="Reject this request?"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-medium bg-white dark:bg-neutral-900 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-neutral-700 rounded-xl hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/30 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Reject
        </button>
        <button wire:click="accept"
                wire:confirm="Accept this request?"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-medium bg-emerald-600 text-white rounded-xl shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          Accept
        </button>
      </div>
    </div>
    @endif

  </div>
</div>
</div>
