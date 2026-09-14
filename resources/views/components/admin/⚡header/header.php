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

        // ✅ FIX: only THIS logged-in user's own notifications — previously
        // pulled every notification in the table regardless of recipient,
        // leaking other users' (students, faculty, program heads) private
        // notifications into whoever happened to be logged in.
        $latest = Notification::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        // ✅ FIX: unread count scoped to this user too
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        // ✅ Map database status 'pending' → 'unread', 'sent' → 'read'
        $this->notifications = $latest->map(fn($n) => [
            'id'       => $n->id,
            'message'  => $n->message,
            'type'     => $n->type,
            'status'   => $n->status === 'pending' ? 'unread' : 'read',
            'time_ago' => $n->created_at->diffForHumans(),
            'user'     => $n->user?->name ?? 'System',
        ])->toArray();
    }

    public function markAsRead(int $id): void
    {
        // ✅ FIX: scoped to the current user so nobody can mark another
        // user's notification as read by guessing/passing an arbitrary ID.
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
        // ✅ FIX: only marks THIS user's own pending notifications as read
        Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'sent']);

        $this->loadNotifications();
    }
};
