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

        // ✅ Get latest 10 notifications FOR THIS COORDINATOR (from notifications table)
        $latest = Notification::with('user')
            ->where('user_id', Auth::id())      // 👈 notifications sent to this coordinator
            ->latest()
            ->take(10)
            ->get();

        // ✅ Count unread (pending) notifications
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        // ✅ Map to dropdown format
        $this->notifications = $latest->map(fn($n) => [
            'id'          => $n->id,
            'type'        => $n->type === 'Gmail' ? 'System' : $n->type,
            'is_facility' => str_contains($n->message, 'facility'), // simple heuristic
            'requester'   => 'Admin',
            'department'  => '—',
            'purpose'     => $n->message,
            'status'      => $n->status,
            'time_ago'    => $n->created_at->diffForHumans(),
        ])->toArray();
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
