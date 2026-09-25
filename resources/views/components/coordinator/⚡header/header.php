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

        // Prefer the linked request's user name. When there's no linked
        // request (e.g. "new submission" notices that didn't get a
        // request_id attached), fall back to parsing the name out of the
        // message text itself, e.g. "jei submitted a facility reservation..."
        $requesterName = $n->request->user->name ?? null;

        if (! $requesterName && preg_match('/^(.*?)\s+submitted/i', $n->message, $matches)) {
            $requesterName = trim($matches[1]);
        }

        $requesterName = $requesterName ?: 'Admin';

        return [
            'id'            => $n->id,
            'type'          => $n->type === 'Gmail' ? 'System' : $n->type,
            'is_facility'   => $isFacility,
            'requester'     => $requesterName,
            'department'    => $n->request->department->department_name ?? '—',
            'purpose'       => $n->request->purpose ?? $n->message,
            'status'        => $n->status,
            'action_status' => $actionStatus,
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
