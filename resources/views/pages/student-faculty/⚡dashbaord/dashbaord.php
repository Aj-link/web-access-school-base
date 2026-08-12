<?php

namespace App\Livewire\StudentFaculty;

use App\Models\ResourceUsage;
use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.student-faculty')] class extends Component
{
    #[Computed]
    public function totalFacilityReservations()
    {
        return ResourceUsage::where('user_id', Auth::id())->count();
    }

    // Facility reservations don't have a status column, so we only show total.
    // For material requests, we can show approved/pending because 'requests' table has status.

    #[Computed]
    public function totalMaterialRequests()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 2) // Material Request
            ->count();
    }

    #[Computed]
    public function approvedMaterialRequests()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 2)
            ->where('status', 'approved')
            ->count();
    }

    #[Computed]
    public function pendingMaterialRequests()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 2)
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function recentActivities()
    {
        // Recent facility reservations (no status, so we just show them)
        $reservations = ResourceUsage::with('resource')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'    => 'Facility',
                'name'    => $item->resource->resource_name ?? '—',
                'date'    => $item->created_at,
                'status'  => 'submitted', // default because no status column
                'details' => Carbon::parse($item->used_date)->format('M d, Y h:i A'),
            ]);

        // Recent material requests (with status)
        $requests = ResourceRequest::with('items')
            ->where('user_id', Auth::id())
            ->where('request_type_id', 2)
            ->latest()
            ->take(5)
            ->get()
            ->flatMap(function ($req) {
                return $req->items->map(fn($item) => [
                    'type'    => 'Material',
                    'name'    => $item->item_name,
                    'date'    => $req->created_at,
                    'status'  => $req->status,
                    'details' => "Qty: {$item->quantity}",
                ]);
            });

        $all = $reservations->concat($requests)->sortByDesc('date')->take(7);

        return $all->map(fn($a) => [
            'type'     => $a['type'],
            'name'     => $a['name'],
            'status'   => $a['status'],
            'details'  => $a['details'],
            'time_ago' => Carbon::parse($a['date'])->diffForHumans(),
        ])->values();
    }

    #[Computed]
    public function monthlyStats()
    {
        // Last 6 months facility reservations count (no status filter)
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = ResourceUsage::where('user_id', Auth::id())
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $months->push([
                'month' => $month->format('M Y'),
                'count' => $count,
            ]);
        }
        return $months;
    }
};
