<?php

use App\Models\Request as ResourceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.student-faculty')] class extends Component
{
    // Facility Reservations and Material Requests are BOTH stored in the
    // `requests` table, distinguished by request_type_id (1 = Facility, 2 = Material).
    // The room/item detail for each lives in `request_items`.

    #[Computed]
    public function totalFacilityReservations()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 1) // Facility Reservation
            ->count();
    }

    #[Computed]
    public function approvedFacilityReservations()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 1)
            ->where('status', 'approved')
            ->count();
    }

    #[Computed]
    public function pendingFacilityReservations()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->where('request_type_id', 1)
            ->where('status', 'pending')
            ->count();
    }

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

    // Combined pending across BOTH Facility and Material requests —
    // used by the "Active (Pending)" stat card, which previously only
    // reflected Material pending.
    #[Computed]
    public function totalPendingRequests()
    {
        return ResourceRequest::where('user_id', Auth::id())
            ->whereIn('request_type_id', [1, 2])
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function recentActivities()
    {
        return ResourceRequest::with('items')
            ->where('user_id', Auth::id())
            ->whereIn('request_type_id', [1, 2])
            ->latest()
            ->take(7)
            ->get()
            ->map(function ($req) {
                $isFacility = (int) $req->request_type_id === 1;
                $firstItem = $req->items->first();

                $details = $isFacility
                    ? ($firstItem
                        ? Carbon::parse($firstItem->request_date)->format('M d, Y')
                            . ($firstItem->start_time ? ' · ' . Carbon::parse($firstItem->start_time)->format('h:i A') : '')
                        : $req->purpose)
                    : ($req->items->count() > 1
                        ? $req->items->count() . ' items'
                        : 'Qty: ' . ($firstItem->quantity ?? '—'));

                return [
                    'type'     => $isFacility ? 'Facility' : 'Material',
                    'name'     => $firstItem->item_name ?? $req->purpose,
                    'status'   => $req->status,
                    'details'  => $details,
                    'time_ago' => $req->created_at->diffForHumans(),
                ];
            });
    }

    #[Computed]
    public function monthlyStats()
    {
        // Last 6 months of facility reservations
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = ResourceRequest::where('user_id', Auth::id())
                ->where('request_type_id', 1) // Facility Reservation
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

    #[Computed]
    public function monthlyMaterialStats()
    {
        // Last 6 months of material requests
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = ResourceRequest::where('user_id', Auth::id())
                ->where('request_type_id', 2) // Material Request
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
