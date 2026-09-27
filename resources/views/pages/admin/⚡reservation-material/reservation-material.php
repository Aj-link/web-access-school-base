<?php

namespace App\Livewire\Admin;

use App\Models\Request as ResourceRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    #[Computed]
    public function requests()
    {
        return ResourceRequest::with(['user.department', 'items'])
            ->where('request_type_id', 2)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function accept(int $id)
    {
        $request = ResourceRequest::with('items')->findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $request->update(['status' => 'approved']);

        DB::table('request_approvals')->updateOrInsert(
            ['request_id' => $request->id, 'approver_id' => Auth::id()],
            [
                'status'      => 'approved',
                'remarks'     => null,
                'approved_at' => now(),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => 'Your material request has been approved by the admin.',
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }

    public function reject(int $id)
    {
        $request = ResourceRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'This request has already been processed.');
            return;
        }

        $request->update(['status' => 'rejected']);

        DB::table('request_approvals')->updateOrInsert(
            ['request_id' => $request->id, 'approver_id' => Auth::id()],
            [
                'status'      => 'rejected',
                'remarks'     => null,
                'approved_at' => now(),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        Notification::create([
            'user_id'    => $request->user_id,
            'request_id' => $request->id,
            'message'    => 'Your material request has been rejected by the admin.',
            'type'       => 'Gmail',
            'status'     => 'pending',
        ]);
    }
};
