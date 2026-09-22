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

        $latest = Notification::with(['request.department', 'request.requestItems'])
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

    private function formatNotification(Notification $n): array
    {
        $message = strtolower($n->message);

        $actionStatus = match (true) {
            str_contains($message, 'rejected') => 'rejected',
            str_contains($message, 'approved') => 'approved',
            default                            => 'info',
        };

        $isFacility = $n->request
            ? $n->request->requestItems->contains(fn ($item) => is_null($item->resource_id))
            : str_contains($message, 'facility');

        return [
            'id'            => $n->id,
            'is_facility'   => $isFacility,
            'purpose'       => $n->request->purpose ?? $n->message,
            'message'       => $n->message,
            'status'        => $n->status,        // pending (unread) / sent (read)
            'action_status' => $actionStatus,     // approved / rejected / info
            'request_id'    => $n->request_id,
            'time_ago'      => $n->created_at->diffForHumans(),
        ];
    }

    public function toggleShowAll(): void
    {
        $this->showAll = ! $this->showAll;
        $this->loadNotifications();
    }

    public function openNotification(int $id): void
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $notification) {
            $this->loadNotifications();
            return;
        }

        if ($notification->status === 'pending') {
            $notification->update(['status' => 'sent']);
        }

        $url = $this->resolveUrl($notification);

        $this->loadNotifications();

        $this->redirect($url, navigate: true);
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'sent']);

        $this->loadNotifications();
    }

    /**
     * Student/faculty notifications are always "here's what happened to
     * YOUR request" (approved/rejected/etc) — so this always points at
     * their own request view, not a review queue like the coordinator's.
     *
     * NOTE: swap 'portal.requests.show' for whatever your actual
     * student/faculty single-request route is named.
     */
    private function resolveUrl(Notification $notification): string
    {
        return route('portal.requests.show', ['request' => $notification->request_id]);
    }
};
