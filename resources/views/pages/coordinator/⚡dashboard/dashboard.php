<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    #[Computed]
    public function totalRequests()
    {
        return ResourceRequest::count();
    }

    #[Computed]
    public function pendingRequests()
    {
        return ResourceRequest::whereIn('status', ['pending', 'admin_review', 'coordinator_review'])->count();
    }

    #[Computed]
    public function approvedRequests()
    {
        return ResourceRequest::where('status', 'approved')->count();
    }

    #[Computed]
    public function rejectedRequests()
    {
        return ResourceRequest::where('status', 'rejected')->count();
    }

    #[Computed]
    public function facilityRequests()
    {
        return ResourceRequest::where('request_type_id', 1)->count();
    }

    #[Computed]
    public function materialRequests()
    {
        return ResourceRequest::where('request_type_id', 2)->count();
    }

    #[Computed]
    public function totalStudents()
    {
        return User::role('student')->count();
    }

    #[Computed]
    public function monthlyData()
    {
        return ResourceRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
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
            ->latest()
            ->take(5)
            ->get();
    }
};
