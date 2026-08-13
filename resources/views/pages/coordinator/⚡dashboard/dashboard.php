<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
    private function deptFilter($q)
    {
        $q->where('department_id', Auth::user()->department_id);
    }

    #[Computed]
    public function totalRequests()
    {
        return ResourceRequest::whereHas('user', fn($q) => $this->deptFilter($q))->count();
    }

    #[Computed]
    public function pendingRequests()
    {
        return ResourceRequest::whereIn('status', ['pending', 'admin_review', 'coordinator_review'])
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->count();
    }

    #[Computed]
    public function approvedRequests()
    {
        return ResourceRequest::where('status', 'approved')
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->count();
    }

    #[Computed]
    public function rejectedRequests()
    {
        return ResourceRequest::where('status', 'rejected')
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->count();
    }

    #[Computed]
    public function facilityRequests()
    {
        return ResourceRequest::where('request_type_id', 1)
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->count();
    }

    #[Computed]
    public function materialRequests()
    {
        return ResourceRequest::where('request_type_id', 2)
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->count();
    }

    #[Computed]
    public function totalStudents()
    {
        return User::role('student')
            ->where('department_id', Auth::user()->department_id)
            ->count();
    }

    #[Computed]
    public function monthlyData()
    {
        return ResourceRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month' => now()->month($r->month)->format('M'),
                'total' => $r->total,
            ]);
    }

    #[Computed]
    public function recentRequests()
    {
        return ResourceRequest::with(['user.department', 'requestType'])
            ->whereIn('status', ['pending', 'coordinator_review'])
            ->whereHas('user', fn($q) => $this->deptFilter($q))
            ->latest()
            ->take(5)
            ->get();
    }
};
