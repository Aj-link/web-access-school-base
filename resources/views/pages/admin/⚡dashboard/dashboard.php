<?php

namespace App\Livewire\Admin;

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
    public function approvalRate()
    {
        $total = $this->totalRequests;
        if ($total === 0) return 0;
        return round(($this->approvedRequests / $total) * 100);
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
public function monthlyData()
{
    $rows = $this->adminVisibleRequests()
        ->whereYear('created_at', now()->year)
        ->get(['created_at']);

    $counts = array_fill(1, 12, 0);

    foreach ($rows as $row) {
        $month = (int) $row->created_at->format('n');
        $counts[$month]++;
    }

    return collect($counts)->map(function ($total, $month) {
        return [
            'month' => now()->month($month)->format('M'),
            'total' => $total,
        ];
    })->values();
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
};
