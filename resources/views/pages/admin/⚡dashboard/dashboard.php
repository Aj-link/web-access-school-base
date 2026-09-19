<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\Request as ResourceRequest;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    /**
     * Only count requests that have reached the admin's visibility scope.
     * Requests still at 'pending' (with student/faculty) are excluded.
     */
    private function adminVisibleRequests()
    {
        return ResourceRequest::whereIn('status', [
            'coordinator_review',
            'admin_review',
            'approved',
            'rejected',
        ]);
    }

    #[Computed]
    public function totalRequests()
    {
        return $this->adminVisibleRequests()->count();
    }

    #[Computed]
    public function pendingRequests()
    {
        // "Pending" for admin = waiting at coordinator or admin level
        return $this->adminVisibleRequests()
            ->whereIn('status', ['coordinator_review', 'admin_review'])
            ->count();
    }

    #[Computed]
    public function approvedRequests()
    {
        return $this->adminVisibleRequests()
            ->where('status', 'approved')
            ->count();
    }

    #[Computed]
    public function rejectedRequests()
    {
        return $this->adminVisibleRequests()
            ->where('status', 'rejected')
            ->count();
    }

    #[Computed]
    public function totalStudents()
    {
        return User::role('student')->count();
    }

    #[Computed]
    public function facilityRequests()
    {
        return $this->adminVisibleRequests()
            ->where('request_type_id', 1)
            ->count();
    }

    #[Computed]
    public function materialRequests()
    {
        return $this->adminVisibleRequests()
            ->where('request_type_id', 2)
            ->count();
    }

    #[Computed]
    public function recentRequests()
    {
        return $this->adminVisibleRequests()
            ->with(['user', 'requestType'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function coordinatorReviewRequests()
    {
        return $this->adminVisibleRequests()
            ->where('status', 'coordinator_review')
            ->count();
    }

    #[Computed]
    public function adminReviewRequests()
    {
        return $this->adminVisibleRequests()
            ->where('status', 'admin_review')
            ->count();
    }

    /**
     * ✅ Updated: monthly totals broken down PER DEPARTMENT,
     * so "Monthly Requests" shows which department leads each month.
     * Shape: [
     *   'labels' => ['Jan', 'Feb', ...],
     *   'departments' => [
     *       ['name' => 'Computer Studies', 'data' => [3, 5, 0, ...]],
     *       ['name' => 'Engineering', 'data' => [1, 2, 4, ...]],
     *       ...
     *   ]
     * ]
     */
    #[Computed]
    public function monthlyData()
    {
        $rows = $this->adminVisibleRequests()
            ->whereYear('created_at', now()->year)
            ->get(['created_at', 'department_id']);

        $labels = collect(range(1, 12))->map(fn ($m) => now()->month($m)->format('M'))->values();

        $departments = Department::all();

        $series = $departments->map(function ($dept) use ($rows) {
            $monthly = array_fill(1, 12, 0);

            $rows->where('department_id', $dept->id)->each(function ($row) use (&$monthly) {
                $month = (int) $row->created_at->format('n');
                $monthly[$month]++;
            });

            return [
                'name' => $dept->department_name,
                'data' => array_values($monthly),
            ];
        })->values();

        return [
            'labels' => $labels,
            'departments' => $series,
        ];
    }
};
