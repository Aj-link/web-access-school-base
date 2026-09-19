<div class="select-none">
<div class="space-y-8 max-w-7xl mx-auto px-6 py-8">

    {{-- Stats Cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Total Requests --}}
        <div class="relative overflow-hidden flex flex-col bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Requests</p>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-mortarboard size-5 text-blue-500 dark:text-blue-400" viewBox="0 0 16 16">
                    <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1.5a.5.5 0 0 1-1 0V11a.5.5 0 0 1 1 0m0 3a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800 dark:text-white">{{ $this->totalRequests }}</h3>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Reached coordinator & above</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 rounded-b-2xl"></div>
        </div>

        {{-- Total Students --}}
        <div class="relative overflow-hidden flex flex-col bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Students</p>
                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-mortarboard size-5.5 text-purple-500 dark:text-purple-400" viewBox="0 0 16 16">
                    <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917zM8 8.46 1.758 5.965 8 3.052l6.242 2.913z"/>
                    <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466zm-.068 1.873.22-.748 3.496 1.311a.5.5 0 0 0 .352 0l3.496-1.311.22.748L8 12.46z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800 dark:text-white">{{ $this->totalStudents }}</h3>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Registered users</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-purple-500 rounded-b-2xl"></div>
        </div>

        {{-- Pending --}}
        <div class="relative overflow-hidden flex flex-col bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Pending Review</p>
                <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-mortarboard size-5 text-yellow-500 dark:text-yellow-400" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800 dark:text-white">{{ $this->pendingRequests }}</h3>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Coordinator & admin level</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-yellow-400 rounded-b-2xl"></div>
        </div>

    </div>

    {{-- Charts Row --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Monthly Requests by Department --}}
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Monthly Requests</h2>
                    <p class="text-sm text-gray-400 dark:text-neutral-500">By department, for {{ now()->year }}</p>
                </div>
                <span class="text-xs bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 px-3 py-1 rounded-full font-medium">This Year</span>
            </div>
            <div class="mt-4">
                <canvas id="monthlyChart" height="130"></canvas>
            </div>
            <div id="monthlyLegend" class="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-4"></div>
        </div>

        {{-- Status Doughnut --}}
        <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Request Status</h2>
            <p class="text-sm text-gray-400 dark:text-neutral-500 mb-4">Overall breakdown</p>
            <canvas id="statusChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                        <span class="text-gray-600 dark:text-neutral-400">Coordinator Review</span>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->coordinatorReviewRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-neutral-400">Admin Review</span>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->adminReviewRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-neutral-400">Approved</span>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->approvedRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-neutral-400">Rejected</span>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->rejectedRequests }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Request Type Chart --}}
    <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Request Types</h2>
                <p class="text-sm text-gray-400 dark:text-neutral-500">Facility Reservations vs Material Requests</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-gray-600 dark:text-neutral-400">Facility — {{ $this->facilityRequests }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                    <span class="text-gray-600 dark:text-neutral-400">Material — {{ $this->materialRequests }}</span>
                </div>
            </div>
        </div>
        <canvas id="typeChart" height="60"></canvas>
    </div>

    {{-- Recent Requests Table --}}
    <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Requests</h2>
                <p class="text-sm text-gray-400 dark:text-neutral-500">Latest 5 requests at coordinator level & above</p>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">Requestor</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">Purpose</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse($this->recentRequests as $request)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-700 text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $request->user->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $request->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($request->request_type_id == 1) bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400
                                @else bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 @endif">
                                {{ $request->requestType->type_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-neutral-400">
                            {{ Str::limit($request->purpose, 40) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($request->status === 'approved') bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400
                                @elseif($request->status === 'rejected') bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400
                                @elseif($request->status === 'admin_review') bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400
                                @elseif($request->status === 'coordinator_review') bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400
                                @endif">
                                @if($request->status === 'approved') Approved
                                @elseif($request->status === 'rejected') Rejected
                                @elseif($request->status === 'admin_review') Admin Review
                                @elseif($request->status === 'coordinator_review') Coordinator Review
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400 dark:text-neutral-500">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 dark:text-neutral-500">No requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ✅ Detect dark mode so chart text/gridlines stay readable on both themes
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';
    const tickColor = isDark ? 'rgba(229,229,229,0.8)' : 'rgba(75,85,99,0.8)';

    // ✅ Monthly Requests, one line per department
    const monthlyLabels = @json($this->monthlyData['labels']);
    const departmentSeries = @json($this->monthlyData['departments']);

    const lineColors = [
        'rgba(34, 197, 94, 1)',   // green
        'rgba(59, 130, 246, 1)',  // blue
        'rgba(168, 85, 247, 1)',  // purple
        'rgba(234, 179, 8, 1)',   // yellow
        'rgba(239, 68, 68, 1)',   // red
        'rgba(20, 184, 166, 1)',  // teal
        'rgba(249, 115, 22, 1)',  // orange
    ];

    const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: departmentSeries.map((dept, i) => ({
                label: dept.name,
                data: dept.data,
                borderColor: lineColors[i % lineColors.length],
                backgroundColor: lineColors[i % lineColors.length].replace('1)', '0.1)'),
                borderWidth: 2.5,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: false,
                tension: 0.35,
            }))
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }, // ✅ using our own legend below instead
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} requests`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: tickColor },
                    grid: { color: gridColor }
                },
                x: { ticks: { color: tickColor }, grid: { display: false } }
            }
        }
    });

    // ✅ Build the custom legend with a real X drawn over the box when hidden
    function renderMonthlyLegend() {
        const legendEl = document.getElementById('monthlyLegend');
        legendEl.innerHTML = '';

        monthlyChart.data.datasets.forEach((dataset, index) => {
            const meta = monthlyChart.getDatasetMeta(index);
            const hidden = meta.hidden === true; // Chart.js sets this on toggle

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'flex items-center gap-1.5 text-sm select-none';
            item.style.opacity = hidden ? '0.5' : '1';

            item.innerHTML = `
                <span class="relative inline-flex items-center justify-center w-3.5 h-3.5 rounded-sm"
                      style="background-color:${dataset.borderColor}">
                    ${hidden ? `
                        <svg class="absolute inset-0 w-full h-full text-white" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="3" y1="3" x2="11" y2="11"/>
                            <line x1="11" y1="3" x2="3" y2="11"/>
                        </svg>
                    ` : ''}
                </span>
                <span class="${hidden ? 'line-through text-gray-400 dark:text-neutral-500' : 'text-gray-600 dark:text-neutral-400'}">
                    ${dataset.label}
                </span>
            `;

            item.addEventListener('click', () => {
                meta.hidden = !hidden;
                monthlyChart.update();
                renderMonthlyLegend();
            });

            legendEl.appendChild(item);
        });
    }

    renderMonthlyLegend();
</script>
</div>
