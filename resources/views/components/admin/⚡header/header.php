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

        // ✅ Get latest 10 notifications (from ANY user – student, faculty, coordinator)
        $latest = Notification::with('user')
            ->latest()
            ->take(10)
            ->get();

        // ✅ Count only 'pending' as unread
        $this->unreadCount = Notification::where('status', 'pending')->count();

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
        $notification = Notification::find($id);
        if ($notification && $notification->status === 'pending') {
            $notification->update(['status' => 'sent']);
        }
        $this->loadNotifications();
    }

    public function markAsUnread(int $id): void
    {
        $notification = Notification::find($id);
        if ($notification && $notification->status === 'sent') {
            $notification->update(['status' => 'pending']);
        }
        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        Notification::where('status', 'pending')->update(['status' => 'sent']);
        $this->loadNotifications();
    }
};
