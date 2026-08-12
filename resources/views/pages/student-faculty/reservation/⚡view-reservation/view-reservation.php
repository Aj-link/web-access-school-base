<?php

namespace App\Livewire\Portal;

use App\Models\Request as ResourceRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.student-faculty')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function reservations()
    {
        return ResourceRequest::with(['items', 'department'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
    }
};
