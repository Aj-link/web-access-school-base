<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public int $unreadCount = 0;
    public array $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
{
    if (! Auth::check()) return;

    $latest = Notification::with('user')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(10)
        ->get();

    $this->unreadCount = Notification::where('user_id', Auth::id())
        ->where('status', 'pending')
        ->count();

    $this->notifications = $latest->map(function ($n) {
        $message = strtolower($n->message);

        // Derive the actual outcome from the message text,
        // since `status` only tracks read/unread, not approve/reject.
        $actionStatus = match (true) {
            str_contains($message, 'rejected') => 'rejected',
            str_contains($message, 'approved')  => 'approved',
            default                              => 'info',
        };

        return [
            'id'            => $n->id,
            'type'          => $n->type === 'Gmail' ? 'System' : $n->type,
            'is_facility'   => str_contains($message, 'facility'),
            'requester'     => 'Admin',
            'department'    => '—',
            'purpose'       => $n->message,
            'status'        => $n->status,        // pending (unread) / sent (read)
            'action_status' => $actionStatus,      // approved / rejected / info
            'time_ago'      => $n->created_at->diffForHumans(),
        ];
    })->toArray();
}

    public function markAsRead(int $id): void
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
        if ($notification && $notification->status === 'pending') {
            $notification->update(['status' => 'sent']);
        }
        $this->loadNotifications();
    }

    public function markAsUnread(int $id): void
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
        if ($notification && $notification->status === 'sent') {
            $notification->update(['status' => 'pending']);
        }
        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'sent']);
        $this->loadNotifications();
    }
};
