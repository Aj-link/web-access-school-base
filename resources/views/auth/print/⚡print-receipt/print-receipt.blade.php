<div class="max-w-2xl mx-auto px-4 py-10 print:py-0 print:px-0">

    @if($notFound)

        {{-- Empty / invalid state --}}
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm p-10 text-center">
            <div class="size-14 rounded-full bg-gray-100 dark:bg-neutral-700 flex items-center justify-center mx-auto mb-4">
                <svg class="size-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-neutral-300">Receipt not available</p>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">
                This request either doesn't exist or hasn't been approved yet.
            </p>
        </div>

    @else

        {{-- Print button --}}
        <div class="flex justify-end mb-4 print:hidden">
            <button onclick="window.print()"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-700 text-white hover:bg-green-800 shadow-sm">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"/>
                </svg>
                Print Receipt
            </button>
        </div>

        {{-- Receipt Card --}}
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm print:shadow-none print:border-0 print:rounded-none overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-4 px-8 pt-8 pb-6 border-b-2 border-green-800 dark:border-green-600">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                    alt="CSAV Logo" class="size-12 rounded-lg shrink-0">
                <div>
                    <h1 class="text-sm font-bold text-green-900 dark:text-green-300">
                        Colegio de Sta. Ana de Victorias
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                        Materials Request &amp; Facility Reservation Receipt
                    </p>
                </div>
            </div>

            <div class="px-8 py-6">

                {{-- Status Badge --}}
                <span class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wide bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-400 mb-6">
                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approved
                </span>

                {{-- Details --}}
                <dl class="grid grid-cols-3 gap-y-3 text-sm mb-8">
                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Receipt No.</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        #{{ $printRequest->id }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Requested By</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $printRequest->user->name }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Department</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $printRequest->user->department->department_name ?? '—' }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Request Type</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $printRequest->requestType->type_name ?? '—' }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Purpose</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $printRequest->purpose }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Approved By</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $approval->approver_name ?? '—' }}
                    </dd>

                    <dt class="col-span-1 text-gray-500 dark:text-neutral-400">Date Approved</dt>
                    <dd class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $approval?->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->format('M d, Y g:i A') : $printRequest->updated_at->format('M d, Y g:i A') }}
                    </dd>
                </dl>

                {{-- Items --}}
                <div class="border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden mb-10 print:border-gray-300">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-900/40">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                    Type
                                </th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                    Item / Facility
                                </th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                    Qty
                                </th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                    Schedule
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                            @foreach($printRequest->items as $item)
                                @php
                                    // A facility line item has no resource_id.
                                    // A material line item always has one.
                                    $isFacility = is_null($item->resource_id);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        @if($isFacility)
                                            <span class="inline-flex items-center gap-x-1 py-0.5 px-2 text-[10px] font-bold uppercase rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400">
                                                Facility
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-x-1 py-0.5 px-2 text-[10px] font-bold uppercase rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-400">
                                                Material
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-neutral-400">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-neutral-400">
                                        @if($item->request_date)
                                            {{ \Carbon\Carbon::parse($item->request_date)->format('M d, Y') }}
                                        @endif
                                        @if($item->start_time && $item->end_time)
                                            <span class="block text-xs">
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($item->end_time)->format('g:i A') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Signatures --}}
                <div class="grid grid-cols-2 gap-10 mt-16 mb-2">
                    <div class="text-center">
                        <div class="border-t border-gray-400 dark:border-neutral-500 pt-2">
                            <p class="text-xs text-gray-500 dark:text-neutral-400">Released By (Property Custodian)</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="border-t border-gray-400 dark:border-neutral-500 pt-2">
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                Received By ({{ $printRequest->user->name }})
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-8 py-4 bg-gray-50 dark:bg-neutral-900/40 border-t border-gray-200 dark:border-neutral-700 text-center">
                <p class="text-xs text-gray-400 dark:text-neutral-500">
                    This receipt confirms an approved request in the CSAV Resource Management System.<br>
                    Present this at pickup / on the reserved date as proof of approval.
                </p>
            </div>

        </div>

    @endif

</div>
