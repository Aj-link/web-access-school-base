<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public int $unreadCount = 0;
    public int $totalCount = 0;
    public bool $showAll = false;
    public array $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        if (! Auth::check()) {
            return;
        }

        // 10 by default, up to 50 when "View all" is toggled
        $latest = Notification::with(['user', 'request.user', 'request.department', 'request.requestItems'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take($this->showAll ? 50 : 10)
            ->get();

        $this->totalCount = Notification::where('user_id', Auth::id())->count();

        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        $this->notifications = $latest->map(fn ($n) => $this->formatNotification($n))->toArray();
    }

    /**
     * Shape a Notification model into the array the Alpine dropdown reads.
     */
    private function formatNotification(Notification $n): array
    {
        $message = strtolower($n->message);

        // action_status: was this a decision (approved/rejected) or just an info/pending notice?
        $actionStatus = match (true) {
            str_contains($message, 'rejected') => 'rejected',
            str_contains($message, 'approved') => 'approved',
            default                            => 'info',
        };

        // Prefer real data from the linked request (works for student/faculty
        // submissions AND Program Head's own submissions to Admin).
        // Falls back to text-parsing for notifications with no request_id
        // (e.g. account-approval notices unrelated to any request).
        $isFacility = $n->request
            ? $n->request->requestItems->contains(fn ($item) => is_null($item->resource_id))
            : str_contains($message, 'facility');

        return [
            'id'            => $n->id,
            'type'          => $n->type === 'Gmail' ? 'System' : $n->type,
            'is_facility'   => $isFacility,
            'requester'     => $n->request->user->name ?? 'Admin',
            'department'    => $n->request->department->department_name ?? '—',
            'purpose'       => $n->request->purpose ?? $n->message,
            'status'        => $n->status,       // pending (unread) / sent (read)
            'action_status' => $actionStatus,    // approved / rejected / info
            'request_id'    => $n->request_id,
            'time_ago'      => $n->created_at->diffForHumans(),
        ];
    }

    // "View all notifications" / "Show less"
    public function toggleShowAll(): void
    {
        $this->showAll = ! $this->showAll;
        $this->loadNotifications();
    }

    // Click a notification: mark as read, then go to the right page
    public function openNotification(int $id): void
    {
        $notification = Notification::with('request.requestItems')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $notification) {
            $this->loadNotifications();
            return;
        }

        if ($notification->status === 'pending') {
            $notification->update(['status' => 'sent']);
        }

        $this->redirect($this->resolveUrl($notification), navigate: true);
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'sent']);

        $this->loadNotifications();
    }

    /**
     * Two directions share this notifications table:
     *
     *  1. "New request submitted" (student/faculty → this Program Head)
     *     → send them to the review list: coordinator.facility or coordinator.material
     *
     *  2. "Your request was approved/rejected" (Admin decided on a request
     *     THIS Program Head submitted) → send them to view-request to see
     *     the outcome of their own submission.
     *
     * Falls back to text-matching "facility" when there's no linked request
     * (e.g. older/legacy notifications created before request_id existed).
     */
    private function resolveUrl(Notification $notification): string
    {
        $message = strtolower($notification->message);
        $isDecision = str_contains($message, 'approved') || str_contains($message, 'rejected');

        if ($isDecision) {
            return route('coordinator.request-to-admin.view-request', [
                'request' => $notification->request_id,
            ]);
        }

        $isFacility = $notification->request
            ? $notification->request->requestItems->contains(fn ($item) => is_null($item->resource_id))
            : str_contains($message, 'facility');

        return $isFacility
            ? route('coordinator.facility')
            : route('coordinator.material');
    }
};
