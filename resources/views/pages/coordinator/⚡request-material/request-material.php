<?php

namespace App\Livewire\Coordinator;

use App\Models\Request as ResourceRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.coordinator')] class extends Component
{
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
        $approverName = Auth::user()->name;

        try {
            DB::transaction(function () use ($request, $departmentId, $approverName) {

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

                foreach ($request->items as $item) {
                    DB::table('resource_all_locations')
                        ->where('resource_id', $item->resource_id)
                        ->where('department_id', $departmentId)
                        ->decrement('allocated_quantity', $item->quantity);
                }

                $request->update(['status' => 'approved']);

                $itemsList = $request->items
                    ->map(fn ($i) => "{$i->item_name} (x{$i->quantity})")
                    ->implode(', ');

                Notification::create([
                    'user_id'    => $request->user_id,
                    'request_id' => $request->id,
                    'message'    => "{$approverName} approved your material request: {$itemsList}.",
                    'type'       => 'Gmail',
                    'status'     => 'pending',
                ]);
            });

            unset($this->requests);
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject(int $id)
    {
        $request = $this->scopedQuery()->with('items')->findOrFail($id);

        $request->update(['status' => 'rejected']);

        $approverName = Auth::user()->name;
        $itemsList = $request->items
            ->map(fn ($i) => "{$i->item_name} (x{$i->quantity})")
            ->implode(', ');

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => "{$approverName} rejected your material request: {$itemsList}.",
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);

        unset($this->requests);
    }
};
