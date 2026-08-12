<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public function mount()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    $user = $user->fresh();

    if ($user->status === 'approved') {
        $this->redirectBasedOnRole($user);
    }

    if ($user->status === 'rejected') {
        Auth::logout();
        return redirect()->route('login');
    }
}

public function checkStatus()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    $user = $user->fresh();

    if ($user->status === 'approved') {
        return $this->redirectBasedOnRole($user);
    }

    if ($user->status === 'rejected') {
        Auth::logout();
        return redirect()->route('login');
    }
}

    private function redirectBasedOnRole($user)
    {
        if ($user->hasRole('coordinator')) {
            return redirect()->route('coordinator.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('portal.dashboard');
    }
};
