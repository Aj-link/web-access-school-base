<?php

use App\Models\Request as ResourceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url]
    public $request_id;

    public ?ResourceRequest $printRequest = null;
    public ?object $approval = null;
    public bool $notFound = false;

    public function mount()
    {
        if (!$this->request_id) {
            $this->notFound = true;
            return;
        }

        $request = ResourceRequest::find($this->request_id);

        if (!$request || $request->status !== 'approved') {
            $this->notFound = true;
            return;
        }

        $user    = Auth::user();
        $isOwner = $request->user_id === $user->id;
        $isStaff = $user->hasRole(['admin', 'coordinator', 'program head']);

        if (!$isOwner && !$isStaff) {
            abort(403);
        }

        $request->load(['user.department', 'items.resource', 'requestType']);

        $this->approval = DB::table('request_approvals')
            ->join('users', 'users.id', '=', 'request_approvals.approver_id')
            ->where('request_approvals.request_id', $request->id)
            ->where('request_approvals.status', 'approved')
            ->select('users.name as approver_name', 'request_approvals.approved_at')
            ->first();

        $this->printRequest = $request;
    }
};
