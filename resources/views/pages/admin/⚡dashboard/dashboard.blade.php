<div class="select-none">
<div class="space-y-8 max-w-7xl mx-auto px-6 py-8">

    {{-- Stats Cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Total Requests --}}
        <div class="relative overflow-hidden flex flex-col bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Total Requests</p>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-3-3v6m9-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800">{{ $this->totalRequests }}</h3>
            <p class="text-xs text-gray-400 mt-1">Reached coordinator & above</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 rounded-b-2xl"></div>
        </div>

        {{-- Approval Rate --}}
        <div class="relative overflow-hidden flex flex-col bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Approval Rate</p>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800">{{ $this->approvalRate }}%</h3>
            <p class="text-xs text-gray-400 mt-1">Of all requests</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-green-500 rounded-b-2xl"></div>
        </div>

        {{-- Total Students --}}
        <div class="relative overflow-hidden flex flex-col bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Total Students</p>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2h5"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800">{{ $this->totalStudents }}</h3>
            <p class="text-xs text-gray-400 mt-1">Registered users</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-purple-500 rounded-b-2xl"></div>
        </div>

        {{-- Pending --}}
        <div class="relative overflow-hidden flex flex-col bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Pending Review</p>
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-800">{{ $this->pendingRequests }}</h3>
            <p class="text-xs text-gray-400 mt-1">Coordinator & admin level</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-yellow-400 rounded-b-2xl"></div>
        </div>

    </div>

    {{-- Charts Row --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Monthly Line Graph --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Monthly Requests</h2>
                    <p class="text-sm text-gray-400">Requests trend for {{ now()->year }}</p>
                </div>
                <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">This Year</span>
            </div>
            <div class="mt-4">
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>

        {{-- Status Doughnut --}}
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">Request Status</h2>
            <p class="text-sm text-gray-400 mb-4">Overall breakdown</p>
            <canvas id="statusChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                        <span class="text-gray-600">Coordinator Review</span>
                    </div>
                    <span class="font-semibold text-gray-800">{{ $this->coordinatorReviewRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                        <span class="text-gray-600">Admin Review</span>
                    </div>
                    <span class="font-semibold text-gray-800">{{ $this->adminReviewRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-gray-600">Approved</span>
                    </div>
                    <span class="font-semibold text-gray-800">{{ $this->approvedRequests }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                        <span class="text-gray-600">Rejected</span>
                    </div>
                    <span class="font-semibold text-gray-800">{{ $this->rejectedRequests }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Request Type Chart --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Request Types</h2>
                <p class="text-sm text-gray-400">Facility Reservations vs Material Requests</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-gray-600">Facility — {{ $this->facilityRequests }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                    <span class="text-gray-600">Material — {{ $this->materialRequests }}</span>
                </div>
            </div>
        </div>
        <canvas id="typeChart" height="60"></canvas>
    </div>

    {{-- Recent Requests Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Recent Requests</h2>
                <p class="text-sm text-gray-400">Latest 5 requests at coordinator level & above</p>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Requestor</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Purpose</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($this->recentRequests as $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-700 text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $request->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $request->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($request->request_type_id == 1) bg-blue-100 text-blue-700
                                @else bg-purple-100 text-purple-700 @endif">
                                {{ $request->requestType->type_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ Str::limit($request->purpose, 40) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($request->status === 'approved') bg-green-100 text-green-700
                                @elseif($request->status === 'rejected') bg-red-100 text-red-700
                                @elseif($request->status === 'admin_review') bg-purple-100 text-purple-700
                                @elseif($request->status === 'coordinator_review') bg-blue-100 text-blue-700
                                @endif">
                                @if($request->status === 'approved') Approved
                                @elseif($request->status === 'rejected') Rejected
                                @elseif($request->status === 'admin_review') Admin Review
                                @elseif($request->status === 'coordinator_review') Coordinator Review
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400">No requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ✅ Fix: monthlyData is a collection of arrays, use array key access
    const monthlyLabels = @json(collect($this->monthlyData)->pluck('month')->values());
    const monthlyTotals = @json(collect($this->monthlyData)->pluck('total')->values());

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Requests',
                data: monthlyTotals,
                borderColor: 'rgba(34, 197, 94, 1)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y + ' requests'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // ✅ Updated: 4 segments matching admin-visible statuses
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Coordinator Review', 'Admin Review', 'Approved', 'Rejected'],
            datasets: [{
                data: [
                    {{ $this->coordinatorReviewRequests }},
                    {{ $this->adminReviewRequests }},
                    {{ $this->approvedRequests }},
                    {{ $this->rejectedRequests }}
                ],
                backgroundColor: [
                    'rgba(96, 165, 250, 0.85)',   // blue-400  — coordinator
                    'rgba(168, 85, 247, 0.85)',   // purple-500 — admin
                    'rgba(34, 197, 94, 0.85)',    // green-500 — approved
                    'rgba(239, 68, 68, 0.85)',    // red-500   — rejected
                ],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: ['Facility Reservation', 'Material Request'],
            datasets: [{
                label: 'Total',
                data: [{{ $this->facilityRequests }}, {{ $this->materialRequests }}],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                ],
                borderRadius: 8,
                barThickness: 50,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>
</div>
