<div class="select-none">
<div class="space-y-8 max-w-7xl mx-auto px-6 py-8">

    {{-- Stats Cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Total Requests --}}
        <div class="relative overflow-hidden flex flex-col bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Requests</p>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-3-3v6m9-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
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
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2h5"/>
                        <circle cx="12" cy="7" r="4"/>
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
                    <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
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
