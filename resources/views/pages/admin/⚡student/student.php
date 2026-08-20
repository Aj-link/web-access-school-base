<?php

use App\Models\User;
use App\Notifications\StudentApprovedNotification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = 'all';
    public string $statusFilter = 'all';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::with('roles', 'department')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['student', 'faculty', 'program head']))
            ->when(
                $this->search,
                fn($q) =>
                $q->where(
                    fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                )
            )
            ->when(
                $this->roleFilter !== 'all',
                fn($q) =>
                $q->whereHas('roles', fn($q) => $q->where('name', $this->roleFilter))
            )
            ->when(
                $this->statusFilter !== 'all',
                fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->paginate(10);
    }

    public function approve(int $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status === 'pending') {
            $user->update(['status' => 'approved']);

            // Send notification for all roles
            try {
                $user->notify(new StudentApprovedNotification());
            } catch (\Exception $e) {
                // Mail may not be configured in dev
            }
        }
    }

    public function reject(int $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status === 'pending') {
            $user->update(['status' => 'rejected']);
        }
    }
};
