<?php

namespace App\Livewire\Coordinator;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.coordinator')] class extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';  // typeFilter removed

    #[Computed]
    public function notifications()
    {
        $query = Notification::where('user_id', Auth::id());

        if ($this->search) {
            $query->where('message', 'like', '%' . $this->search . '%');
        }
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(15);
    }

    #[Computed]
    public function unreadCount()
    {
        return Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function readCount()
    {
        return Notification::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->count();
    }

    #[Computed]
    public function failedCount()
    {
        return Notification::where('user_id', Auth::id())
            ->where('status', 'failed')
            ->count();
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        if ($notification) {
            $notification->update(['status' => 'sent']);
        }
    }

    public function markAsUnread($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        if ($notification) {
            $notification->update(['status' => 'pending']);
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'sent']);
    }

    public function deleteNotification($id)
    {
        Notification::where('id', $id)->where('user_id', Auth::id())->delete();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
    }
};
