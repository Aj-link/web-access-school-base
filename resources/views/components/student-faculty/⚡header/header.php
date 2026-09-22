<?php

use App\Models\Request;
use App\Models\RequestApproval;
use App\Models\Notification;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $showRejectModal = false;
    public ?int $rejectingRequestId = null;
    public string $rejectRemarks = '';

    #[Computed]
    public function pendingRequests()
    {
        return Request::with(['user', 'department', 'items.resource', 'requestType'])
            ->where('department_id', Auth::user()->department_id)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function approveRequest(int $requestId): void
    {
        $request = Request::with('items.resource')->findOrFail($requestId);

        if ($request->department_id !== Auth::user()->department_id) {
            abort(403);
        }

        DB::transaction(function () use ($request) {
            // Pass 1: validate stock for every item that needs a resource,
            // regardless of whether this is a facility reservation with
            // attached materials or a standalone material request.
            foreach ($request->items as $item) {
                if (! $item->resource_id) {
                    continue;
                }

                $resource = Resource::lockForUpdate()->findOrFail($item->resource_id);

                if ($resource->quantity_available < $item->quantity) {
                    throw new \RuntimeException(
                        "Not enough stock for \"{$resource->resource_name}\". Available: {$resource->quantity_available}, requested: {$item->quantity}."
                    );
                }
            }

            // Pass 2: deduct, now that we know every item can be fulfilled.
            foreach ($request->items as $item) {
                if (! $item->resource_id) {
                    continue;
                }

                Resource::where('id', $item->resource_id)
                    ->decrement('quantity_available', $item->quantity);
            }

            $request->update(['status' => 'approved']);

            RequestApproval::create([
                'request_id'  => $request->id,
                'approver_id' => Auth::id(),
                'status'      => 'approved',
                'approved_at' => now(),
            ]);

            Notification::create([
                'user_id'    => $request->user_id,
                'request_id' => $request->id,
                'message'    => "Your request \"{$request->purpose}\" has been approved.",
                'type'       => 'Gmail',
                'status'     => 'pending',
            ]);
        });

        unset($this->pendingRequests);
    }

    public function openReject(int $requestId): void
    {
        $this->rejectingRequestId = $requestId;
        $this->rejectRemarks = '';
        $this->showRejectModal = true;
    }

    public function cancelReject(): void
    {
        $this->showRejectModal = false;
        $this->rejectingRequestId = null;
        $this->rejectRemarks = '';
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectRemarks' => 'required|string|min:3',
        ]);

        $request = Request::findOrFail($this->rejectingRequestId);

        if ($request->department_id !== Auth::user()->department_id) {
            abort(403);
        }

        DB::transaction(function () use ($request) {
            $request->update(['status' => 'rejected']);

            RequestApproval::create([
                'request_id'  => $request->id,
                'approver_id' => Auth::id(),
                'status'      => 'rejected',
                'remarks'     => $this->rejectRemarks,
                'approved_at' => now(),
            ]);

            Notification::create([
                'user_id'    => $request->user_id,
                'request_id' => $request->id,
                'message'    => "Your request \"{$request->purpose}\" was rejected: {$this->rejectRemarks}",
                'type'       => 'Gmail',
                'status'     => 'pending',
            ]);
        });

        $this->cancelReject();
        unset($this->pendingRequests);
    }
};
