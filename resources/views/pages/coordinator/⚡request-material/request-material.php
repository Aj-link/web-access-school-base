<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
    /**
     * Base query scope shared by the listing and the action guards.
     * Scoped by the request's own department_id (set at creation time),
     * not the requestor's current department — so a program head only
     * ever sees requests filed under their own department.
     *
     * Also restricted to requests submitted by Student/Faculty accounts
     * only — Admin or Program Head accounts should never show up here.
     */
    protected function scopedQuery()
    {
        return ResourceRequest::where('request_type_id', 2)
            ->where('department_id', Auth::user()->department_id)
            ->whereHas('user', function ($query) {
                $query->role(['student', 'faculty']);
            });
    }

    #[Computed]
    public function requests()
    {
        return $this->scopedQuery()
            ->with(['user.department', 'items'])
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        $request = $this->scopedQuery()->with('items')->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $departmentId = Auth::user()->department_id;

        try {
            DB::transaction(function () use ($request, $departmentId) {

                // Pass 1: make sure every item has enough allocated stock before touching anything
                foreach ($request->items as $item) {
                    $allocation = DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->lockForUpdate()
                        ->first();

                    if (!$allocation || $allocation->allocated_quantity < $item->quantity) {
                        throw new \RuntimeException(
                            "Not enough stock for \"{$item->item_name}\". Please request a restock from Admin first."
                        );
                    }
                }

                // Pass 2: deduct, now that every item is confirmed fulfillable
                foreach ($request->items as $item) {
                    DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->decrement('allocated_quantity', $item->quantity);
                }

                $request->update(['status' => 'approved']);
            });

            unset($this->requests);
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject(int $id)
    {
        $request = $this->scopedQuery()->findOrFail($id);

        $request->update(['status' => 'rejected']);

        unset($this->requests);
    }
};